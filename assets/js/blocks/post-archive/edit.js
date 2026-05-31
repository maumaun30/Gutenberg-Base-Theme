import { useEffect, useState } from '@wordpress/element';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  SelectControl,
  RangeControl,
  ToggleControl,
  Spinner,
  Placeholder,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
  const { sectionLabel, postType, postsPerPage, showLoadMore, columns } = attributes;

  const [posts, setPosts] = useState([]);
  const [postTypes, setPostTypes] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');

  // Fetch available public post types and build REST base map
  useEffect(() => {
    apiFetch({ path: '/wp/v2/types' }).then((types) => {
      const options = Object.entries(types)
        .filter(([, t]) => t.viewable !== false && t.rest_base)
        .map(([slug, t]) => ({
          label: t.name,
          value: t.rest_base, // store REST base as value
        }));
      setPostTypes(options);
    }).catch(() => {});
  }, []);

  // Fetch posts whenever postType or postsPerPage changes
  useEffect(() => {
    setIsLoading(true);
    setError('');

    // postType attribute stores the REST base (e.g. "posts", "pages", "games")
    const restBase = postType || 'posts';

    apiFetch({
      path: `/wp/v2/${restBase}?per_page=${postsPerPage}&_embed=1&status=publish`,
    })
      .then((data) => {
        setPosts(Array.isArray(data) ? data : []);
        setIsLoading(false);
      })
      .catch((err) => {
        setError(`Could not load posts for "${restBase}". Check the post type REST base.`);
        setPosts([]);
        setIsLoading(false);
      });
  }, [postType, postsPerPage]);

  const blockProps = useBlockProps({ className: 'fnlmx-post-archive' });

  const gridStyle = {
    display: 'grid',
    gridTemplateColumns: `repeat(${columns}, 1fr)`,
    gap: '8px',
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Archive Settings')} initialOpen={true}>
          <TextControl
            label={__('Section Label')}
            value={sectionLabel}
            onChange={(value) => setAttributes({ sectionLabel: value })}
          />
          <SelectControl
            label={__('Post Type')}
            value={postType}
            options={postTypes.length ? postTypes : [{ label: 'Posts', value: 'posts' }]}
            onChange={(value) => setAttributes({ postType: value })}
            help={__('Displays all public post types registered with REST API support.')}
          />
          <RangeControl
            label={__('Posts Per Page')}
            value={postsPerPage}
            onChange={(value) => setAttributes({ postsPerPage: value })}
            min={2}
            max={20}
          />
          <RangeControl
            label={__('Columns')}
            value={columns}
            onChange={(value) => setAttributes({ columns: value })}
            min={1}
            max={4}
          />
          <ToggleControl
            label={__('Show Load More Button')}
            checked={showLoadMore}
            onChange={(value) => setAttributes({ showLoadMore: value })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {/* Section label */}
        {sectionLabel && (
          <p className="fnlmx-post-archive__label">{sectionLabel}</p>
        )}

        {/* Grid */}
        {isLoading ? (
          <div style={{ padding: '40px', display: 'flex', alignItems: 'center', gap: '12px' }}>
            <Spinner />
            <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: '13px' }}>Loading posts…</span>
          </div>
        ) : error ? (
          <Placeholder label={__('Post Archive')} instructions={error} />
        ) : posts.length === 0 ? (
          <Placeholder
            label={__('No posts found')}
            instructions={__('No published posts found for this post type. Try publishing some posts first.')}
          />
        ) : (
          <div className="fnlmx-post-archive__grid" style={gridStyle}>
            {posts.map((post) => {
              const thumb =
                post._embedded?.['wp:featuredmedia']?.[0]?.source_url || '';
              const title = post.title?.rendered || __('Untitled');
              const excerpt = post.excerpt?.rendered
                ? post.excerpt.rendered.replace(/<[^>]+>/g, '').substring(0, 80) + '…'
                : '';

              return (
                <article key={post.id} className="fnlmx-archive-card">
                  <div className="fnlmx-archive-card__thumb">
                    {thumb ? (
                      <img src={thumb} alt={title} />
                    ) : (
                      <div className="fnlmx-archive-card__thumb-placeholder" />
                    )}
                    <div className="fnlmx-archive-card__overlay" />
                  </div>

                  <div className="fnlmx-archive-card__content">
                    <h3
                      className="fnlmx-archive-card__title"
                      dangerouslySetInnerHTML={{ __html: title }}
                    />
                    {excerpt && (
                      <p className="fnlmx-archive-card__excerpt">{excerpt}</p>
                    )}
                    <a href={post.link} className="fnlmx-archive-card__btn">
                      {__('Read More')}
                    </a>
                  </div>
                </article>
              );
            })}
          </div>
        )}

        {/* Load More */}
        {showLoadMore && !isLoading && (
          <div className="fnlmx-post-archive__load-more-wrap">
            <button className="fnlmx-post-archive__load-more" disabled>
              {__('Load More')}
            </button>
            <p className="fnlmx-post-archive__load-more-note">
              {__('(Load More is active on the front end)')}
            </p>
          </div>
        )}
      </div>
    </>
  );
}
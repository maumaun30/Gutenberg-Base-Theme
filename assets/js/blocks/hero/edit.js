import { useEffect, useState } from '@wordpress/element';
import {
  RichText,
  InspectorControls,
  useBlockProps,
  ColorPalette,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  ToggleControl,
  SelectControl,
  RangeControl,
  __experimentalInputControl as InputControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    subtitle,
    breadcrumbLabel,
    ctaText,
    ctaUrl,
    selectedPostType,
    imageCount,
    backgroundColor,
    accentColor,
    showBreadcrumb,
    showCta,
    showPostImages,
  } = attributes;

  const [postTypes, setPostTypes] = useState([]);
  const [previewPosts, setPreviewPosts] = useState([]);
  const [totalPosts, setTotalPosts] = useState(0);

  // Fetch available post types (excluding internal WP types)
  useEffect(() => {
    apiFetch({ path: '/wp/v2/types?context=edit' }).then((types) => {
      const excluded = ['attachment', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation', 'wp_font_family', 'wp_font_face'];
      const options = Object.entries(types)
        .filter(([key]) => !excluded.includes(key))
        .map(([key, val]) => ({ label: val.name, value: key }));
      setPostTypes(options);
    });
  }, []);

  // Fetch preview posts whenever postType or imageCount changes
  useEffect(() => {
    if (!selectedPostType) return;
    apiFetch({
      path: `/wp/v2/${selectedPostType === 'post' ? 'posts' : selectedPostType}?per_page=${imageCount}&_fields=id,title,featured_media,_links&_embed`,
    })
      .then((posts) => {
        setPreviewPosts(posts);
      })
      .catch(() => setPreviewPosts([]));

    // Get total count
    apiFetch({
      path: `/wp/v2/${selectedPostType === 'post' ? 'posts' : selectedPostType}?per_page=1`,
      parse: false,
    })
      .then((res) => {
        setTotalPosts(parseInt(res.headers.get('X-WP-Total'), 10) || 0);
      })
      .catch(() => setTotalPosts(0));
  }, [selectedPostType, imageCount]);

  const blockProps = useBlockProps({ style: { '--hero-bg': backgroundColor, '--hero-accent': accentColor } });

  const breadcrumbDisplay = breadcrumbLabel || title || 'Page Title';

  return (
    <>
      <InspectorControls>
        {/* ── Content ─────────────────────────────── */}
        <PanelBody title="Content" initialOpen={true}>
          <ToggleControl
            label="Show Breadcrumb"
            checked={showBreadcrumb}
            onChange={(val) => setAttributes({ showBreadcrumb: val })}
          />
          {showBreadcrumb && (
            <TextControl
              label="Breadcrumb Label (leave blank to use Title)"
              value={breadcrumbLabel}
              onChange={(val) => setAttributes({ breadcrumbLabel: val })}
              help="Displayed as: Home > [this label]"
            />
          )}
          <ToggleControl
            label="Show CTA Button"
            checked={showCta}
            onChange={(val) => setAttributes({ showCta: val })}
          />
          {showCta && (
            <>
              <TextControl
                label="Button Text"
                value={ctaText}
                onChange={(val) => setAttributes({ ctaText: val })}
              />
              <TextControl
                label="Button URL"
                value={ctaUrl}
                onChange={(val) => setAttributes({ ctaUrl: val })}
              />
            </>
          )}
        </PanelBody>

        {/* ── Post Type Images ─────────────────────── */}
        <PanelBody title="Post Type Image Display" initialOpen={true}>
          <ToggleControl
            label="Show Post Images"
            checked={showPostImages}
            onChange={(val) => setAttributes({ showPostImages: val })}
          />
          {showPostImages && (
            <>
              <SelectControl
                label="Post Type"
                value={selectedPostType}
                options={postTypes.length ? postTypes : [{ label: 'Loading…', value: 'post' }]}
                onChange={(val) => setAttributes({ selectedPostType: val })}
                help={totalPosts ? `${totalPosts} items found` : ''}
              />
              <RangeControl
                label="Number of Images to Show"
                value={imageCount}
                onChange={(val) => setAttributes({ imageCount: val })}
                min={1}
                max={6}
              />
            </>
          )}
        </PanelBody>

        {/* ── Colors ──────────────────────────────── */}
        <PanelBody title="Colors" initialOpen={false}>
          <p style={{ fontWeight: 600, marginBottom: 8 }}>Background Color</p>
          <ColorPalette
            value={backgroundColor}
            onChange={(val) => setAttributes({ backgroundColor: val || '#7B1FA2' })}
          />
          <p style={{ fontWeight: 600, marginBottom: 8, marginTop: 12 }}>Accent Color</p>
          <ColorPalette
            value={accentColor}
            onChange={(val) => setAttributes({ accentColor: val || '#F5C518' })}
          />
        </PanelBody>
      </InspectorControls>

      {/* ── Editor Preview ───────────────────────── */}
      <div {...blockProps} className={`wp-block-mytheme-hero ${blockProps.className || ''}`}>
        {showBreadcrumb && (
          <nav className="hero-breadcrumb" aria-label="Breadcrumb">
            <span className="breadcrumb-home">Home</span>
            <span className="breadcrumb-sep"> &rsaquo; </span>
            <span className="breadcrumb-current">{breadcrumbDisplay}</span>
          </nav>
        )}

        <div className="hero-inner">
          <div className="hero-content">
            <RichText
              tagName="h1"
              className="hero-title"
              value={title}
              onChange={(val) => setAttributes({ title: val })}
              placeholder="Enter hero title…"
            />
            {subtitle !== undefined && (
              <RichText
                tagName="p"
                className="hero-subtitle"
                value={subtitle}
                onChange={(val) => setAttributes({ subtitle: val })}
                placeholder="Optional subtitle…"
              />
            )}
            {showCta && (
              <a href={ctaUrl} className="hero-cta" onClick={(e) => e.preventDefault()}>
                {ctaText}
              </a>
            )}
          </div>

          {showPostImages && (
            <div className="hero-images">
              {previewPosts.length > 0 ? (
                previewPosts.map((post) => {
                  const img = post._embedded?.['wp:featuredmedia']?.[0]?.source_url;
                  return (
                    <div key={post.id} className="hero-image-item">
                      {img ? (
                        <img src={img} alt={post.title?.rendered || ''} />
                      ) : (
                        <div className="hero-image-placeholder">
                          <span>No Image</span>
                        </div>
                      )}
                    </div>
                  );
                })
              ) : (
                <div className="hero-images-empty">
                  <span>📷 Select a post type to show images</span>
                  {totalPosts > 0 && <small>{totalPosts} posts found</small>}
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
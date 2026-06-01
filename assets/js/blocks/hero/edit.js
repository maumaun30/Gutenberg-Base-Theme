import { useEffect, useState } from '@wordpress/element';
import {
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  SelectControl,
  RangeControl,
  Spinner,
  Notice,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

export default function Edit({ attributes, setAttributes }) {
  const {
    selectedPostType,
    slideCount,
    breadcrumbLabel,
    autoplayDelay,
    sliderHeight,
    overlayOpacity,
  } = attributes;

  const [postTypes,   setPostTypes]   = useState([]);
  const [slides,      setSlides]      = useState([]);
  const [totalPosts,  setTotalPosts]  = useState(0);
  const [loading,     setLoading]     = useState(false);
  const [activeSlide, setActiveSlide] = useState(0);

  // Fetch all registered public post types
  useEffect(() => {
    apiFetch({ path: '/wp/v2/types?context=edit' }).then((types) => {
      const excluded = [
        'attachment','wp_block','wp_template','wp_template_part',
        'wp_navigation','wp_font_family','wp_font_face',
      ];
      const options = Object.entries(types)
        .filter(([key]) => !excluded.includes(key))
        .map(([key, val]) => ({ label: val.name, value: key }));
      setPostTypes(options);
    });
  }, []);

  // Fetch featured images for preview
  useEffect(() => {
    if (!selectedPostType) return;
    setLoading(true);
    setActiveSlide(0);
    const endpoint = selectedPostType === 'post' ? 'posts' : selectedPostType;

    apiFetch({
      path: `/wp/v2/${endpoint}?per_page=${slideCount}&_fields=id,title,featured_media,_links&_embed`,
    })
      .then((posts) => {
        const built = posts
          .map((p) => ({
            id:    p.id,
            title: p.title?.rendered || '',
            img:   p._embedded?.['wp:featuredmedia']?.[0]?.source_url || '',
          }))
          .filter((s) => s.img);
        setSlides(built);
        setLoading(false);
      })
      .catch(() => { setSlides([]); setLoading(false); });

    apiFetch({ path: `/wp/v2/${endpoint}?per_page=1`, parse: false })
      .then((res) => setTotalPosts(parseInt(res.headers.get('X-WP-Total'), 10) || 0))
      .catch(() => setTotalPosts(0));
  }, [selectedPostType, slideCount]);

  const blockProps = useBlockProps({
    style: {
      position:   'relative',
      height:     sliderHeight,
      overflow:   'hidden',
      background: '#111',
      display:    'block',
    },
  });

  const currentSlide      = slides[activeSlide];
  const breadcrumbDisplay = breadcrumbLabel || 'Page Title';

  return (
    <>
      {/* ── Inspector Controls ───────────────────────────────── */}
      <InspectorControls>

        <PanelBody title="Post Type" initialOpen={true}>
          <SelectControl
            label="Select Post Type"
            value={selectedPostType}
            options={postTypes.length ? postTypes : [{ label: 'Loading…', value: 'post' }]}
            onChange={(val) => setAttributes({ selectedPostType: val })}
            help={totalPosts ? `${totalPosts} total posts found` : ''}
          />
          <RangeControl
            label="Number of Slides"
            value={slideCount}
            onChange={(val) => setAttributes({ slideCount: val })}
            min={1}
            max={10}
          />
        </PanelBody>

        <PanelBody title="Breadcrumb" initialOpen={true}>
          <TextControl
            label="Override Breadcrumb Label"
            value={breadcrumbLabel}
            onChange={(val) => setAttributes({ breadcrumbLabel: val })}
            help="Leave blank — auto-resolves to the current page title on the front-end."
          />
        </PanelBody>

        <PanelBody title="Slider Settings" initialOpen={false}>
          <TextControl
            label="Slider Height (CSS value)"
            value={sliderHeight}
            onChange={(val) => setAttributes({ sliderHeight: val })}
            help="e.g. 420px · 50vh · 480px"
          />
          <RangeControl
            label="Autoplay Delay (ms)"
            value={autoplayDelay}
            onChange={(val) => setAttributes({ autoplayDelay: val })}
            min={2000}
            max={10000}
            step={500}
          />
          <RangeControl
            label="Overlay Darkness (%)"
            value={overlayOpacity}
            onChange={(val) => setAttributes({ overlayOpacity: val })}
            min={0}
            max={80}
            step={5}
          />
        </PanelBody>

      </InspectorControls>

      {/* ── Editor Canvas ────────────────────────────────────── */}
      <div
        {...blockProps}
        className={`wp-block-mytheme-hero hero-slider ${blockProps.className || ''}`}
      >
        {/* Slide background */}
        {loading ? (
          <div style={{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', zIndex:2 }}>
            <Spinner />
          </div>
        ) : currentSlide ? (
          <>
            <img
              src={currentSlide.img}
              alt={currentSlide.title}
              style={{
                position: 'absolute', inset: 0,
                width: '100%', height: '100%',
                objectFit: 'cover', objectPosition: 'center',
                display: 'block',
              }}
            />
            <div style={{
              position: 'absolute', inset: 0,
              background: `rgba(0,0,0,${overlayOpacity / 100})`,
              zIndex: 1,
            }} />
          </>
        ) : (
          <div style={{
            position: 'absolute', inset: 0, zIndex: 2,
            display: 'flex', alignItems: 'center', justifyContent: 'center',
          }}>
            <Notice status="warning" isDismissible={false} style={{ maxWidth: 340 }}>
              No posts with featured images found for <strong>{selectedPostType}</strong>.
            </Notice>
          </div>
        )}

        {/* Breadcrumb — nav-container aligned */}
        <div style={{
          position: 'absolute', top: 0, left: 0, right: 0, zIndex: 10,
          display: 'flex', alignItems: 'center',
          width: '100%', paddingTop: '14px',
        }}>
          <nav style={{
            display: 'flex', alignItems: 'center', gap: '2px',
            width: '100%', maxWidth: '1280px', margin: '0 auto', padding: '0 24px',
            fontFamily: "'Montserrat', sans-serif",
            fontSize: '13px', fontWeight: 500,
            letterSpacing: '-0.8px', textTransform: 'uppercase', lineHeight: 1,
          }}>
            <span style={{ color: 'var(--nav-text-muted, #A1A1AA)' }}>Home</span>
            <span style={{ color: 'var(--nav-text-muted, #A1A1AA)', opacity: 0.7, padding: '0 2px' }}> › </span>
            <span style={{ color: 'var(--color-nav-active, #BA001D)', fontWeight: 700 }}>
              {breadcrumbDisplay}
            </span>
          </nav>
        </div>

        {/* Dot navigation — click to preview in editor */}
        {slides.length > 1 && (
          <div style={{
            position: 'absolute', bottom: 20, left: '50%',
            transform: 'translateX(-50%)', zIndex: 10,
            display: 'flex', alignItems: 'center', gap: '8px',
          }}>
            {slides.map((_, i) => (
              <button
                key={i}
                onClick={() => setActiveSlide(i)}
                aria-label={`Go to slide ${i + 1}`}
                style={{
                  width:        i === activeSlide ? 14 : 10,
                  height:       i === activeSlide ? 14 : 10,
                  borderRadius: '50%',
                  border:       'none',
                  cursor:       'pointer',
                  padding:      0,
                  flexShrink:   0,
                  background:   i === activeSlide
                    ? 'var(--color-primary, #BA001D)'
                    : 'rgba(255,255,255,0.35)',
                  transition: 'all 0.25s ease',
                }}
              />
            ))}
          </div>
        )}
      </div>
    </>
  );
}
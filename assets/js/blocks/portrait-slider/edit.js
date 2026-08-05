import {
  useBlockProps,
  RichText,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
  TextareaControl,
  ToggleControl,
  RangeControl,
  Placeholder,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    titleHighlight,
    subtitle,
    isPageTitle,
    slides,
    autoplay,
    autoplayDelay,
    speed,
    continuous,
    centeredSlides,
    showPagination,
  } = attributes;

  const list = slides || [];

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  const updateSlide = (index, changes) => {
    setAttributes({
      slides: list.map((slide, i) => (i === index ? { ...slide, ...changes } : slide)),
    });
  };

  const removeSlide = (index) => setAttributes({ slides: list.filter((_, i) => i !== index) });

  const moveSlide = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ slides: next });
  };

  // A multi-select add, so a set of screenshots can be dropped in at once.
  const addSlides = (media) => {
    const picked = Array.isArray(media) ? media : [media];
    setAttributes({
      slides: [
        ...list,
        ...picked.map((item) => ({
          imageUrl: item.url,
          imageId: item.id,
          imageAlt: item.alt || '',
          linkUrl: '',
        })),
      ],
    });
  };

  const blockProps = useBlockProps({ className: 'portrait-slider-editor' });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Section" initialOpen={true}>
          <ToggleControl
            label="Use as page title (H1)"
            help="Turn on only when this section heading is the main heading of the page."
            checked={!!isPageTitle}
            onChange={(value) => setAttributes({ isPageTitle: value })}
          />
          <TextareaControl
            label="Description / Subtext"
            help="Optional. Hidden on the front end when left empty."
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
          />
        </PanelBody>

        <PanelBody title="Slider" initialOpen={false}>
          <ToggleControl
            label="Autoplay"
            checked={!!autoplay}
            onChange={(value) => setAttributes({ autoplay: value })}
          />
          {autoplay && (
            <RangeControl
              label="Autoplay Delay (ms)"
              value={autoplayDelay}
              onChange={(value) => setAttributes({ autoplayDelay: value })}
              min={1000}
              max={8000}
              step={250}
            />
          )}
          <RangeControl
            label="Transition Speed (ms)"
            value={speed}
            onChange={(value) => setAttributes({ speed: value })}
            min={200}
            max={2000}
            step={50}
          />
          <ToggleControl
            label="Continuous Loop"
            help={
              continuous
                ? 'Runs on without end, wrapping from the last slide back to the first.'
                : 'Stops at the last slide instead of wrapping around.'
            }
            checked={!!continuous}
            onChange={(value) => setAttributes({ continuous: value })}
          />
          <ToggleControl
            label="Centered Slides"
            help="Highlights the middle slide, as in the design."
            checked={!!centeredSlides}
            onChange={(value) => setAttributes({ centeredSlides: value })}
          />
          <ToggleControl
            label="Show Pagination Dots"
            checked={!!showPagination}
            onChange={(value) => setAttributes({ showPagination: value })}
          />
        </PanelBody>

        {list.map((slide, index) => (
          <PanelBody key={index} title={`Slide ${index + 1}`} initialOpen={false}>
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateSlide(index, {
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={slide.imageId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    {slide.imageUrl && (
                      <img
                        src={slide.imageUrl}
                        alt={slide.imageAlt}
                        style={{ width: '100%', display: 'block', marginBottom: '8px' }}
                      />
                    )}
                    <Button variant="secondary" size="small" onClick={open}>
                      {slide.imageUrl ? 'Replace Image' : 'Select Image'}
                    </Button>
                  </div>
                )}
              />
            </MediaUploadCheck>

            <TextControl
              label="Link (optional)"
              help="Wraps the slide in a link when set."
              value={slide.linkUrl}
              type="url"
              onChange={(value) => updateSlide(index, { linkUrl: value })}
            />

            <div style={{ display: 'flex', gap: '8px' }}>
              <Button variant="secondary" size="small" onClick={() => moveSlide(index, -1)} disabled={index === 0}>
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveSlide(index, 1)}
                disabled={index === list.length - 1}
              >
                Move Down
              </Button>
              <Button variant="tertiary" isDestructive size="small" onClick={() => removeSlide(index)}>
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Slides" initialOpen={true}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={addSlides}
              allowedTypes={['image']}
              multiple
              gallery={false}
              render={({ open }) => (
                <Button variant="primary" onClick={open}>
                  Add Slide(s)
                </Button>
              )}
            />
          </MediaUploadCheck>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="portrait-slider-editor__header">
          <TitleTag className="portrait-slider-editor__title">
            <RichText
              tagName="span"
              value={title}
              onChange={(value) => setAttributes({ title: value })}
              placeholder="Enter title…"
              allowedFormats={[]}
            />

            {' '}

            <span className="highlight-text">
              <RichText
                tagName="span"
                value={titleHighlight}
                onChange={(value) => setAttributes({ titleHighlight: value })}
                placeholder="Highlighted text…"
                allowedFormats={[]}
              />
            </span>
          </TitleTag>

          {/* Placeholder keeps the field reachable while empty; render.php drops
              it entirely on the front end. */}
          <RichText
            tagName="p"
            className="portrait-slider-editor__subtitle"
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
            placeholder="Optional subtext — leave empty to hide it on the front end."
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {list.length ? (
          <>
            {/* Static strip — the slider only runs on the front end so it does
                not fight with editing. */}
            <div className="portrait-slider-editor__strip">
              {list.map((slide, index) => (
                <div key={index} className="portrait-slider-editor__slide">
                  {slide.imageUrl ? (
                    <img src={slide.imageUrl} alt={slide.imageAlt} />
                  ) : (
                    <span className="portrait-slider-editor__slide-empty">No image</span>
                  )}
                  <Button
                    variant="tertiary"
                    isDestructive
                    size="small"
                    onClick={() => removeSlide(index)}
                  >
                    Remove
                  </Button>
                </div>
              ))}
            </div>

            {/* Mirrors the front end, so the sidebar toggle has a visible
                effect here too: one dot per image, with the first active
                because the editor strip always starts at the first slide.
                Past 7 images the front end switches to a travelling window of
                5, which is what the cap here stands in for. */}
            {!!showPagination && (
              <div className="portrait-slider-editor__dots">
                {list.slice(0, list.length > 7 ? 5 : list.length).map(
                  (_slide, index) => (
                    <span
                      key={index}
                      className={
                        'portrait-slider-editor__dot' +
                        (index === 0 ? ' is-active' : '')
                      }
                    />
                  )
                )}
              </div>
            )}

            <div className="portrait-slider-editor__add">
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={addSlides}
                  allowedTypes={['image']}
                  multiple
                  gallery={false}
                  render={({ open }) => (
                    <Button variant="secondary" onClick={open}>
                      Add Slide(s)
                    </Button>
                  )}
                />
              </MediaUploadCheck>
            </div>
          </>
        ) : (
          <Placeholder
            className="portrait-slider-editor__placeholder"
            label="Portrait Slider"
            instructions="No slides yet. Add your first image — you can select several at once."
          >
            <MediaUploadCheck>
              <MediaUpload
                onSelect={addSlides}
                allowedTypes={['image']}
                multiple
                gallery={false}
                render={({ open }) => (
                  <Button variant="primary" onClick={open}>
                    Add Slide(s)
                  </Button>
                )}
              />
            </MediaUploadCheck>
          </Placeholder>
        )}
      </div>
    </>
  );
}

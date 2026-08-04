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
  ToggleControl,
  RangeControl,
  ResponsiveWrapper,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const {
    imageUrl,
    imageId,
    imageAlt,
    title,
    titleHighlight,
    subtitle,
    features,
    primaryButtonText,
    showPrimaryButton,
    secondaryButtonText,
    secondaryButtonUrl,
    isPageTitle,
    overlayOpacity,
  } = attributes;

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  const list = features || [];

  const updateFeature = (index, value) => {
    const next = [...list];
    next[index] = value;
    setAttributes({ features: next });
  };

  const overlayStyle = {
    background: `linear-gradient(to right, rgba(10, 10, 11, ${overlayOpacity / 100}), rgba(10, 10, 11, ${(overlayOpacity / 100) * 0.6}))`,
  };

  const blockProps = useBlockProps({ className: 'hero-banner-editor' });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Banner Image" initialOpen={true}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={(media) =>
                setAttributes({
                  imageUrl: media.url,
                  imageId: media.id,
                  imageAlt: media.alt || '',
                })
              }
              allowedTypes={['image']}
              value={imageId}
              render={({ open }) => (
                <div>
                  {imageUrl ? (
                    <>
                      <ResponsiveWrapper naturalWidth={1200} naturalHeight={900}>
                        <img src={imageUrl} alt={imageAlt} style={{ width: '100%', display: 'block' }} />
                      </ResponsiveWrapper>
                      <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                        <Button variant="secondary" onClick={open} isSmall>
                          Replace Image
                        </Button>
                        <Button
                          variant="tertiary"
                          isDestructive
                          isSmall
                          onClick={() => setAttributes({ imageUrl: '', imageId: 0, imageAlt: '' })}
                        >
                          Remove
                        </Button>
                      </div>
                    </>
                  ) : (
                    <Button variant="primary" onClick={open}>
                      Select Image
                    </Button>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>

          <RangeControl
            label="Overlay Opacity (%)"
            value={overlayOpacity}
            onChange={(value) => setAttributes({ overlayOpacity: value })}
            min={0}
            max={100}
            style={{ marginTop: '16px' }}
          />

          <ToggleControl
            label="Use as page title (H1)"
            help="Turn on only when this banner is the main heading of the page."
            checked={!!isPageTitle}
            onChange={(value) => setAttributes({ isPageTitle: value })}
          />
        </PanelBody>

        <PanelBody title="Feature List" initialOpen={false}>
          {list.map((item, index) => (
            <div key={index} style={{ display: 'flex', gap: '4px', alignItems: 'flex-end' }}>
              <div style={{ flex: 1 }}>
                <TextControl
                  label={`Item ${index + 1}`}
                  value={item}
                  onChange={(value) => updateFeature(index, value)}
                />
              </div>
              <Button
                variant="tertiary"
                isDestructive
                isSmall
                style={{ marginBottom: '8px' }}
                onClick={() =>
                  setAttributes({ features: list.filter((_, i) => i !== index) })
                }
              >
                Remove
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={() => setAttributes({ features: [...list, ''] })}>
            Add Item
          </Button>
        </PanelBody>

        <PanelBody title="Primary Button" initialOpen={false}>
          <ToggleControl
            label="Show Primary Button"
            help="When on, the primary button is shown and opens the Login / Register modal."
            checked={!!showPrimaryButton}
            onChange={(value) => setAttributes({ showPrimaryButton: value })}
          />
          {showPrimaryButton && (
            <TextControl
              label="Button Text"
              value={primaryButtonText}
              onChange={(value) => setAttributes({ primaryButtonText: value })}
            />
          )}
        </PanelBody>

        <PanelBody title="Secondary Button" initialOpen={false}>
          <TextControl
            label="Button Text"
            value={secondaryButtonText}
            onChange={(value) => setAttributes({ secondaryButtonText: value })}
          />
          <TextControl
            label="Button URL"
            value={secondaryButtonUrl}
            onChange={(value) => setAttributes({ secondaryButtonUrl: value })}
            type="url"
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {/* Overlay */}
        <div className="hero-banner-editor__overlay" style={overlayStyle} aria-hidden="true" />

        <div className="hero-banner-editor__content">
          {/* Text column */}
          <div className="hero-banner-editor__inner">
            <TitleTag className="hero-banner-editor__title">
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
                  placeholder="Enter highlighted text…"
                  allowedFormats={[]}
                />
              </span>
            </TitleTag>

            <RichText
              tagName="p"
              className="hero-banner-editor__subtitle"
              value={subtitle}
              onChange={(value) => setAttributes({ subtitle: value })}
              placeholder="Enter subtitle…"
              allowedFormats={['core/bold', 'core/italic']}
            />

            {((showPrimaryButton && primaryButtonText) || secondaryButtonText) && (
              <div className="hero-banner-editor__buttons">
                {showPrimaryButton && primaryButtonText && (
                  <span className="hero-banner-editor__btn hero-banner-editor__btn--primary">
                    {primaryButtonText}
                  </span>
                )}
                {secondaryButtonText && (
                  <span className="hero-banner-editor__btn hero-banner-editor__btn--secondary">
                    {secondaryButtonText}
                  </span>
                )}
              </div>
            )}

            {!!list.filter(Boolean).length && (
              <ul className="hero-banner-editor__features">
                {list.filter(Boolean).map((item, index) => (
                  <li key={index} className="hero-banner-editor__feature">
                    <span className="hero-banner-editor__check" aria-hidden="true">✓</span>
                    {item}
                  </li>
                ))}
              </ul>
            )}
          </div>

          {/* Image column */}
          <div className="hero-banner-editor__media">
            {imageUrl ? (
              <img src={imageUrl} alt={imageAlt} className="hero-banner-editor__image" />
            ) : (
              <div className="hero-banner-editor__placeholder">Select an image</div>
            )}
          </div>
        </div>
      </div>
    </>
  );
}

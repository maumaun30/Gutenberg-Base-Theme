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
  SelectControl,
} from '@wordpress/components';

// Same shape as the feature-section block's buttons, so the two stay
// interchangeable for editors.
const EMPTY_DOWNLOAD_BUTTON = {
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
  smallText: '',
  mainText: 'Button',
  action: 'link',
  url: '',
  fileUrl: '',
  fileId: 0,
  fileName: '',
  newTab: false,
};

export default function Edit({ attributes, setAttributes }) {
  const {
    imageUrl,
    imageId,
    imageAlt,
    title,
    titleHighlight,
    subtitle,
    features,
    downloadButtons,
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
  const buttonList = downloadButtons || [];

  const updateFeature = (index, value) => {
    const next = [...list];
    next[index] = value;
    setAttributes({ features: next });
  };

  const updateButton = (index, changes) => {
    setAttributes({
      downloadButtons: buttonList.map((entry, i) =>
        i === index ? { ...entry, ...changes } : entry
      ),
    });
  };

  const removeButton = (index) => {
    setAttributes({ downloadButtons: buttonList.filter((_, i) => i !== index) });
  };

  const moveButton = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= buttonList.length) {
      return;
    }
    const next = [...buttonList];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ downloadButtons: next });
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

        {buttonList.map((button, index) => (
          <PanelBody
            key={`download-button-${index}`}
            title={`Download Button ${index + 1}: ${button.mainText || ''}`}
            initialOpen={false}
          >
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateButton(index, {
                    iconUrl: media.url,
                    iconId: media.id,
                    iconAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={button.iconId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    <p style={{ margin: '0 0 4px', fontSize: '11px', textTransform: 'uppercase' }}>
                      Button Icon
                    </p>
                    {button.iconUrl ? (
                      <>
                        <img
                          src={button.iconUrl}
                          alt={button.iconAlt}
                          style={{ width: '36px', height: '36px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" size="small" onClick={open}>
                            Replace
                          </Button>
                          <Button
                            variant="tertiary"
                            isDestructive
                            size="small"
                            onClick={() => updateButton(index, { iconUrl: '', iconId: 0, iconAlt: '' })}
                          >
                            Remove
                          </Button>
                        </div>
                      </>
                    ) : (
                      <Button variant="secondary" size="small" onClick={open}>
                        Upload Icon
                      </Button>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>

            <TextControl
              label="Small Text (top line)"
              help="Optional, e.g. “Available on the”."
              value={button.smallText}
              onChange={(value) => updateButton(index, { smallText: value })}
            />
            <TextControl
              label="Main Text (bottom line)"
              value={button.mainText}
              onChange={(value) => updateButton(index, { mainText: value })}
            />

            <SelectControl
              label="Action"
              value={button.action || 'link'}
              options={[
                { label: 'Go to link', value: 'link' },
                { label: 'Download file (APK, PDF…)', value: 'file' },
              ]}
              onChange={(value) => updateButton(index, { action: value })}
            />

            {(button.action || 'link') === 'link' ? (
              <>
                <TextControl
                  label="URL"
                  value={button.url}
                  type="url"
                  onChange={(value) => updateButton(index, { url: value })}
                />
                <ToggleControl
                  label="Open in New Tab"
                  checked={!!button.newTab}
                  onChange={(value) => updateButton(index, { newTab: value })}
                />
              </>
            ) : (
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) =>
                    updateButton(index, {
                      fileUrl: media.url,
                      fileId: media.id,
                      fileName: media.filename || media.title || '',
                    })
                  }
                  allowedTypes={[]}
                  value={button.fileId}
                  render={({ open }) => (
                    <div>
                      {button.fileUrl ? (
                        <>
                          <p style={{ margin: '0 0 8px', wordBreak: 'break-all', fontSize: '12px' }}>
                            {button.fileName || button.fileUrl}
                          </p>
                          <div style={{ display: 'flex', gap: '8px' }}>
                            <Button variant="secondary" size="small" onClick={open}>
                              Replace File
                            </Button>
                            <Button
                              variant="tertiary"
                              isDestructive
                              size="small"
                              onClick={() => updateButton(index, { fileUrl: '', fileId: 0, fileName: '' })}
                            >
                              Remove
                            </Button>
                          </div>
                        </>
                      ) : (
                        <Button variant="secondary" onClick={open}>
                          Select / Upload File
                        </Button>
                      )}
                      <p style={{ marginTop: '8px', fontSize: '12px', fontStyle: 'italic' }}>
                        APK files may need to be allowed in Media Library uploads.
                      </p>
                    </div>
                  )}
                />
              </MediaUploadCheck>
            )}

            <div style={{ display: 'flex', gap: '8px', marginTop: '12px' }}>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveButton(index, -1)}
                disabled={index === 0}
              >
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveButton(index, 1)}
                disabled={index === buttonList.length - 1}
              >
                Move Down
              </Button>
              <Button variant="tertiary" isDestructive size="small" onClick={() => removeButton(index)}>
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Download Button" initialOpen={true}>
          <Button
            variant="primary"
            onClick={() =>
              setAttributes({ downloadButtons: [...buttonList, { ...EMPTY_DOWNLOAD_BUTTON }] })
            }
          >
            Add Download Button
          </Button>
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

            {!!buttonList.length && (
              <div className="hero-banner-editor__download-buttons">
                {buttonList.map((button, index) => (
                  <span key={index} className="hero-banner-editor__download-btn">
                    {button.iconUrl && (
                      <span className="hero-banner-editor__download-btn-icon">
                        <img src={button.iconUrl} alt="" />
                      </span>
                    )}
                    <span className="hero-banner-editor__download-btn-text">
                      {button.smallText && (
                        <span className="hero-banner-editor__download-btn-small">{button.smallText}</span>
                      )}
                      <span className="hero-banner-editor__download-btn-main">{button.mainText}</span>
                    </span>
                  </span>
                ))}
              </div>
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

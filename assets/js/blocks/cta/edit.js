import {
  useBlockProps,
  RichText,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
  ResponsiveWrapper,
} from '@wordpress/components';
import { plus, trash } from '@wordpress/icons';

// Checkmark icon for bullet preview
function CheckIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
      <path d="M5 13l4 4L19 7" />
    </svg>
  );
}

export default function Edit({ attributes, setAttributes }) {
  const {
    imageId, imageUrl, imageAlt,
    title, body,
    bulletPoints,
    primaryButtonText, primaryButtonUrl,
    secondaryButtonText, secondaryButtonUrl,
  } = attributes;

  // ── Bullet repeater helpers ───────────────────────────────────────
  const updateBullet = (index, value) => {
    const updated = bulletPoints.map((b, i) => i === index ? value : b);
    setAttributes({ bulletPoints: updated });
  };

  const addBullet = () => setAttributes({ bulletPoints: [...bulletPoints, 'New bullet point'] });

  const removeBullet = (index) => setAttributes({ bulletPoints: bulletPoints.filter((_, i) => i !== index) });

  const blockProps = useBlockProps({
    className: 'cta-editor-wrapper',
    style: { backgroundColor: 'var(--bg-dark-2, #111)', padding: '48px 32px', borderRadius: '8px' },
  });

  return (
    <>
      <InspectorControls>
        {/* Image */}
        <PanelBody title="Image" initialOpen={true}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={(media) => setAttributes({ imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' })}
              allowedTypes={['image']}
              value={imageId}
              render={({ open }) => (
                <>
                  {imageUrl ? (
                    <>
                      <ResponsiveWrapper naturalWidth={800} naturalHeight={600}>
                        <img src={imageUrl} alt={imageAlt} style={{ width: '100%', display: 'block' }} />
                      </ResponsiveWrapper>
                      <div style={{ display: 'flex', gap: '8px', marginTop: '8px' }}>
                        <Button variant="secondary" isSmall onClick={open}>Replace</Button>
                        <Button variant="tertiary" isSmall isDestructive onClick={() => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' })}>Remove</Button>
                      </div>
                    </>
                  ) : (
                    <Button variant="primary" onClick={open}>Select Image</Button>
                  )}
                </>
              )}
            />
          </MediaUploadCheck>
        </PanelBody>

        {/* Content */}
        <PanelBody title="Content" initialOpen={true}>
          <TextControl
            label="Title"
            value={title}
            onChange={(value) => setAttributes({ title: value })}
          />
          <TextareaControl
            label="Body Text"
            value={body}
            onChange={(value) => setAttributes({ body: value })}
            rows={4}
          />
        </PanelBody>

        {/* Bullet points */}
        <PanelBody title={`Bullet Points (${bulletPoints.length})`} initialOpen={true}>
          {bulletPoints.map((bullet, index) => (
            <div key={index} style={{ display: 'flex', gap: '8px', alignItems: 'center', marginBottom: '8px' }}>
              <div style={{ flex: 1 }}>
                <TextControl
                  value={bullet}
                  onChange={(value) => updateBullet(index, value)}
                  hideLabelFromVision
                  label={`Bullet ${index + 1}`}
                />
              </div>
              <Button
                icon={trash}
                isSmall
                isDestructive
                disabled={bulletPoints.length <= 1}
                onClick={() => removeBullet(index)}
                label="Remove"
              />
            </div>
          ))}
          <Button
            icon={plus}
            variant="secondary"
            onClick={addBullet}
            style={{ width: '100%', justifyContent: 'center', marginTop: '4px' }}
          >
            Add Bullet
          </Button>
        </PanelBody>

        {/* Buttons */}
        <PanelBody title="Primary Button" initialOpen={false}>
          <TextControl label="Text" value={primaryButtonText} onChange={(v) => setAttributes({ primaryButtonText: v })} />
          <TextControl label="URL" value={primaryButtonUrl} onChange={(v) => setAttributes({ primaryButtonUrl: v })} type="url" />
        </PanelBody>
        <PanelBody title="Secondary Button" initialOpen={false}>
          <TextControl label="Text" value={secondaryButtonText} onChange={(v) => setAttributes({ secondaryButtonText: v })} />
          <TextControl label="URL" value={secondaryButtonUrl} onChange={(v) => setAttributes({ secondaryButtonUrl: v })} type="url" />
        </PanelBody>
      </InspectorControls>

      {/* ── Canvas preview ── */}
      <div {...blockProps}>
        <div className="cta-editor__grid">

          {/* Left: image */}
          <div className="cta-editor__image-col">
            <div className="cta-editor__image-wrap">
              {imageUrl
                ? <img src={imageUrl} alt={imageAlt} style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} />
                : (
                  <div className="cta-editor__image-placeholder">
                    <span>Select an image in the sidebar</span>
                  </div>
                )
              }
            </div>
            {/* Floating accent blob */}
            <div className="cta-editor__accent" />
          </div>

          {/* Right: content */}
          <div className="cta-editor__content-col">
            <RichText
              tagName="h2"
              className="cta-editor__title"
              value={title}
              onChange={(value) => setAttributes({ title: value })}
              placeholder="Enter CTA title…"
              allowedFormats={[]}
            />
            <RichText
              tagName="p"
              className="cta-editor__body"
              value={body}
              onChange={(value) => setAttributes({ body: value })}
              placeholder="Enter body text…"
              allowedFormats={['core/bold', 'core/italic']}
            />

            <ul className="cta-editor__bullets">
              {bulletPoints.map((bullet, index) => (
                <li key={index} className="cta-editor__bullet">
                  <span className="cta-editor__bullet-icon"><CheckIcon /></span>
                  <span>{bullet}</span>
                </li>
              ))}
            </ul>

            <div className="cta-editor__buttons">
              <span className="cta-editor__btn cta-editor__btn--primary">{primaryButtonText}</span>
              <span className="cta-editor__btn cta-editor__btn--secondary">{secondaryButtonText}</span>
            </div>
          </div>

        </div>
      </div>
    </>
  );
}
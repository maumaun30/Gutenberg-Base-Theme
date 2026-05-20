import {
  InspectorControls,
  useBlockProps,
  MediaUpload,
  MediaUploadCheck,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Reusable image-picker for a feature icon slot.
 */
function FeatureIconPicker({ label, iconId, iconUrl, onSelect, onRemove }) {
  return (
    <div style={{ marginBottom: '16px' }}>
      <p style={{ fontWeight: 600, marginBottom: '6px', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.06em' }}>
        {label}
      </p>
      {iconUrl && (
        <img
          src={iconUrl}
          alt=""
          style={{ width: '48px', height: '48px', objectFit: 'contain', display: 'block', marginBottom: '8px', borderRadius: '6px', background: 'rgba(247,29,194,0.08)', padding: '6px' }}
        />
      )}
      <MediaUploadCheck>
        <MediaUpload
          onSelect={(media) => onSelect(media)}
          allowedTypes={['image']}
          value={iconId}
          render={({ open }) => (
            <Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>
              {iconUrl ? __('Replace Icon Image') : __('Select Icon Image')}
            </Button>
          )}
        />
      </MediaUploadCheck>
      {iconUrl && (
        <Button
          variant="link"
          isDestructive
          onClick={onRemove}
          style={{ marginTop: '6px', width: '100%', textAlign: 'center' }}
        >
          {__('Remove')}
        </Button>
      )}
    </div>
  );
}

/**
 * Renders the badge preview: only non-empty fields, pink dots auto-inserted between them.
 */
function BadgePreview({ badge1, badge2, badge3 }) {
  const parts = [badge1, badge2, badge3].filter((v) => v && v.trim() !== '');
  if (parts.length === 0) return null;
  return (
    <div className="lva-badge">
      {parts.map((part, i) => (
        <span key={i}>
          {i > 0 && <span className="lva-badge__dot">•</span>}
          <span className="lva-badge__item">{part}</span>
        </span>
      ))}
    </div>
  );
}

export default function Edit({ attributes, setAttributes }) {
  const {
    badge1, badge2, badge3,
    headingTop, headingHighlight, headingBottom,
    description, closingText,
    feature1IconId, feature1IconUrl, feature1Title, feature1Desc,
    feature2IconId, feature2IconUrl, feature2Title, feature2Desc,
    feature3IconId, feature3IconUrl, feature3Title, feature3Desc,
    mediaImageUrl, mediaImageAlt, mediaImageId,
  } = attributes;

  const blockProps = useBlockProps({ className: 'lva-section' });

  const features = [
    { iconUrl: feature1IconUrl, title: feature1Title, desc: feature1Desc, num: 1 },
    { iconUrl: feature2IconUrl, title: feature2Title, desc: feature2Desc, num: 2 },
    { iconUrl: feature3IconUrl, title: feature3Title, desc: feature3Desc, num: 3 },
  ];

  return (
    <>
      <InspectorControls>

        {/* Heading & Badge */}
        <PanelBody title={__('Heading & Badge')} initialOpen={true}>
          <p style={{ fontSize: '11px', fontWeight: 600, textTransform: 'uppercase', letterSpacing: '0.06em', marginBottom: '4px' }}>
            {__('Badge Items (pink dot auto-separates non-empty)')}
          </p>
          <TextControl
            label={__('Badge Item 1')}
            value={badge1}
            onChange={(v) => setAttributes({ badge1: v })}
          />
          <TextControl
            label={__('Badge Item 2')}
            value={badge2}
            onChange={(v) => setAttributes({ badge2: v })}
          />
          <TextControl
            label={__('Badge Item 3')}
            value={badge3}
            onChange={(v) => setAttributes({ badge3: v })}
          />
          <TextControl
            label={__('Heading Line 1')}
            value={headingTop}
            onChange={(v) => setAttributes({ headingTop: v })}
          />
          <TextControl
            label={__('Heading Highlight (pink)')}
            value={headingHighlight}
            onChange={(v) => setAttributes({ headingHighlight: v })}
          />
          <TextControl
            label={__('Heading Line 3')}
            value={headingBottom}
            onChange={(v) => setAttributes({ headingBottom: v })}
          />
        </PanelBody>

        {/* Description */}
        <PanelBody title={__('Description Text')} initialOpen={false}>
          <TextareaControl
            label={__('Opening Description')}
            value={description}
            onChange={(v) => setAttributes({ description: v })}
          />
          <TextareaControl
            label={__('Closing Text')}
            value={closingText}
            onChange={(v) => setAttributes({ closingText: v })}
          />
        </PanelBody>

        {/* Feature 1 */}
        <PanelBody title={__('Feature 1')} initialOpen={false}>
          <FeatureIconPicker
            label={__('Icon Image')}
            iconId={feature1IconId}
            iconUrl={feature1IconUrl}
            onSelect={(media) => setAttributes({ feature1IconId: media.id, feature1IconUrl: media.url })}
            onRemove={() => setAttributes({ feature1IconId: 0, feature1IconUrl: '' })}
          />
          <TextControl
            label={__('Title')}
            value={feature1Title}
            onChange={(v) => setAttributes({ feature1Title: v })}
          />
          <TextControl
            label={__('Description')}
            value={feature1Desc}
            onChange={(v) => setAttributes({ feature1Desc: v })}
          />
        </PanelBody>

        {/* Feature 2 */}
        <PanelBody title={__('Feature 2')} initialOpen={false}>
          <FeatureIconPicker
            label={__('Icon Image')}
            iconId={feature2IconId}
            iconUrl={feature2IconUrl}
            onSelect={(media) => setAttributes({ feature2IconId: media.id, feature2IconUrl: media.url })}
            onRemove={() => setAttributes({ feature2IconId: 0, feature2IconUrl: '' })}
          />
          <TextControl
            label={__('Title')}
            value={feature2Title}
            onChange={(v) => setAttributes({ feature2Title: v })}
          />
          <TextControl
            label={__('Description')}
            value={feature2Desc}
            onChange={(v) => setAttributes({ feature2Desc: v })}
          />
        </PanelBody>

        {/* Feature 3 */}
        <PanelBody title={__('Feature 3')} initialOpen={false}>
          <FeatureIconPicker
            label={__('Icon Image')}
            iconId={feature3IconId}
            iconUrl={feature3IconUrl}
            onSelect={(media) => setAttributes({ feature3IconId: media.id, feature3IconUrl: media.url })}
            onRemove={() => setAttributes({ feature3IconId: 0, feature3IconUrl: '' })}
          />
          <TextControl
            label={__('Title')}
            value={feature3Title}
            onChange={(v) => setAttributes({ feature3Title: v })}
          />
          <TextControl
            label={__('Description')}
            value={feature3Desc}
            onChange={(v) => setAttributes({ feature3Desc: v })}
          />
        </PanelBody>

        {/* Section Image */}
        <PanelBody title={__('Section Image')} initialOpen={false}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={(media) =>
                setAttributes({ mediaImageId: media.id, mediaImageUrl: media.url, mediaImageAlt: media.alt || '' })
              }
              allowedTypes={['image']}
              value={mediaImageId}
              render={({ open }) => (
                <div>
                  {mediaImageUrl && (
                    <img src={mediaImageUrl} alt={mediaImageAlt} style={{ width: '100%', marginBottom: '8px', borderRadius: '6px' }} />
                  )}
                  <Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>
                    {mediaImageUrl ? __('Replace Image') : __('Select Image')}
                  </Button>
                  {mediaImageUrl && (
                    <Button
                      variant="link"
                      isDestructive
                      onClick={() => setAttributes({ mediaImageId: 0, mediaImageUrl: '', mediaImageAlt: '' })}
                      style={{ marginTop: '8px', width: '100%', textAlign: 'center' }}
                    >
                      {__('Remove Image')}
                    </Button>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>
          {mediaImageUrl && (
            <TextControl
              label={__('Alt Text')}
              value={mediaImageAlt}
              onChange={(v) => setAttributes({ mediaImageAlt: v })}
            />
          )}
        </PanelBody>

      </InspectorControls>

      {/* ── Editor Preview ── */}
      <section {...blockProps}>
        <div className="lva-section__inner">

          {/* Left: Content */}
          <div className="lva-content">

            <BadgePreview badge1={badge1} badge2={badge2} badge3={badge3} />

            <h2 className="lva-heading">
              {headingTop}<br />
              <span className="lva-heading__highlight">{headingHighlight}</span><br />
              {headingBottom}
            </h2>

            <p className="lva-description">{description}</p>

            <div className="lva-features">
              {features.map((f) => (
                <div className="lva-feature" key={f.num}>
                  <div className="lva-feature__icon">
                    {f.iconUrl
                      ? <img src={f.iconUrl} alt="" width="32" height="32" style={{ objectFit: 'contain' }} />
                      : <span style={{ fontSize: '10px', color: 'rgba(247,29,194,0.5)' }}>icon</span>
                    }
                  </div>
                  <div className="lva-feature__text">
                    <strong className={`lva-feature__title lva-feature__title--${f.num}`}>{f.title}</strong>
                    <span className={`lva-feature__desc lva-feature__desc--${f.num}`}>{f.desc}</span>
                  </div>
                </div>
              ))}
            </div>

            <p className="lva-closing">{closingText}</p>
          </div>

          {/* Right: Media */}
          <div className="lva-media">
            {mediaImageUrl
              ? <img src={mediaImageUrl} alt={mediaImageAlt} className="lva-media__img" />
              : <div className="lva-media__placeholder"><span>Select section image in sidebar →</span></div>
            }
          </div>

        </div>
      </section>
    </>
  );
}
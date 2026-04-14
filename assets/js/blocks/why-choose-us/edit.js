import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
  Card,
  CardBody,
  CardHeader,
  Flex,
  FlexItem,
  FlexBlock,
} from '@wordpress/components';
import { plus, chevronUp, chevronDown, trash } from '@wordpress/icons';

// Placeholder shown in editor when no SVG is uploaded yet
function SvgPlaceholder() {
  return (
    <svg
      viewBox="0 0 64 64"
      xmlns="http://www.w3.org/2000/svg"
      style={{ width: '32px', height: '32px', opacity: 0.3 }}
    >
      <rect x="8" y="8" width="48" height="48" rx="4" fill="none" stroke="currentColor" strokeWidth="3" strokeDasharray="6 3" />
      <text x="32" y="38" textAnchor="middle" fontSize="20" fill="currentColor">SVG</text>
    </svg>
  );
}

export default function Edit({ attributes, setAttributes }) {
  const { sectionTitle, sectionSubtitle, features } = attributes;

  // ── Repeater helpers ──────────────────────────────────────────────

  const updateFeature = (index, field, value) => {
    const updated = features.map((f, i) =>
      i === index ? { ...f, [field]: value } : f
    );
    setAttributes({ features: updated });
  };

  const updateFeatureSvg = (index, media) => {
    const updated = features.map((f, i) =>
      i === index ? { ...f, svgId: media.id, svgUrl: media.url } : f
    );
    setAttributes({ features: updated });
  };

  const clearFeatureSvg = (index) => {
    const updated = features.map((f, i) =>
      i === index ? { ...f, svgId: 0, svgUrl: '' } : f
    );
    setAttributes({ features: updated });
  };

  const addFeature = () => {
    setAttributes({
      features: [
        ...features,
        { svgId: 0, svgUrl: '', title: 'New Feature', description: 'Describe this feature.' },
      ],
    });
  };

  const removeFeature = (index) => {
    setAttributes({ features: features.filter((_, i) => i !== index) });
  };

  const moveFeature = (index, direction) => {
    const updated = [...features];
    const target = index + direction;
    if (target < 0 || target >= updated.length) return;
    [updated[index], updated[target]] = [updated[target], updated[index]];
    setAttributes({ features: updated });
  };

  const blockProps = useBlockProps({
    className: 'why-choose-us-editor',
    style: {
      backgroundColor: 'var(--bg-dark-3, #0f0f0f)',
      padding: '48px 32px',
      borderRadius: '8px',
    },
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Section Heading" initialOpen={true}>
          <TextControl
            label="Title"
            value={sectionTitle}
            onChange={(value) => setAttributes({ sectionTitle: value })}
          />
          <TextareaControl
            label="Subtitle"
            value={sectionSubtitle}
            onChange={(value) => setAttributes({ sectionSubtitle: value })}
            rows={2}
          />
        </PanelBody>

        <PanelBody title={`Features (${features.length})`} initialOpen={true}>
          {features.map((feature, index) => (
            <Card key={index} style={{ marginBottom: '12px', border: '1px solid #444' }}>
              <CardHeader>
                <Flex align="center">
                  <FlexItem>
                    <strong style={{ color: '#aaa', fontSize: '12px' }}>
                      Feature {index + 1}
                    </strong>
                  </FlexItem>
                  <FlexBlock />
                  <FlexItem>
                    <Button icon={chevronUp} isSmall disabled={index === 0} onClick={() => moveFeature(index, -1)} label="Move up" />
                  </FlexItem>
                  <FlexItem>
                    <Button icon={chevronDown} isSmall disabled={index === features.length - 1} onClick={() => moveFeature(index, 1)} label="Move down" />
                  </FlexItem>
                  <FlexItem>
                    <Button icon={trash} isSmall isDestructive disabled={features.length <= 1} onClick={() => removeFeature(index)} label="Remove" />
                  </FlexItem>
                </Flex>
              </CardHeader>

              <CardBody>
                {/* SVG upload field */}
                <p style={{ fontSize: '11px', color: '#aaa', margin: '0 0 8px', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                  Icon (SVG)
                </p>
                <MediaUploadCheck>
                  <MediaUpload
                    onSelect={(media) => updateFeatureSvg(index, media)}
                    allowedTypes={['image/svg+xml']}
                    value={feature.svgId}
                    render={({ open }) => (
                      <Flex align="center" gap={2} style={{ marginBottom: '12px' }}>
                        {/* Preview */}
                        <FlexItem>
                          <div
                            style={{
                              width: '48px',
                              height: '48px',
                              borderRadius: '8px',
                              backgroundColor: 'var(--bg-dark-2, #1a1a1a)',
                              border: '1px solid #444',
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              overflow: 'hidden',
                            }}
                          >
                            {feature.svgUrl
                              ? <img src={feature.svgUrl} alt="" style={{ width: '28px', height: '28px', objectFit: 'contain' }} />
                              : <SvgPlaceholder />
                            }
                          </div>
                        </FlexItem>

                        {/* Buttons */}
                        <FlexBlock>
                          <Button variant="secondary" isSmall onClick={open} style={{ marginBottom: '4px', display: 'block', width: '100%' }}>
                            {feature.svgUrl ? 'Replace SVG' : 'Upload SVG'}
                          </Button>
                          {feature.svgUrl && (
                            <Button variant="tertiary" isSmall isDestructive onClick={() => clearFeatureSvg(index)} style={{ display: 'block', width: '100%' }}>
                              Remove
                            </Button>
                          )}
                        </FlexBlock>
                      </Flex>
                    )}
                  />
                </MediaUploadCheck>

                <TextControl
                  label="Title"
                  value={feature.title}
                  onChange={(value) => updateFeature(index, 'title', value)}
                />
                <TextareaControl
                  label="Description"
                  value={feature.description}
                  onChange={(value) => updateFeature(index, 'description', value)}
                  rows={3}
                />
              </CardBody>
            </Card>
          ))}

          <Button
            icon={plus}
            variant="secondary"
            onClick={addFeature}
            style={{ width: '100%', justifyContent: 'center', marginTop: '4px' }}
          >
            Add Feature
          </Button>
        </PanelBody>
      </InspectorControls>

      {/* ── Editor canvas preview ── */}
      <div {...blockProps}>
        <div style={{ textAlign: 'center', marginBottom: '48px' }}>
          <RichText
            tagName="h2"
            value={sectionTitle}
            onChange={(value) => setAttributes({ sectionTitle: value })}
            placeholder="Section title…"
            style={{
              fontFamily: "'Bebas Neue', sans-serif",
              fontSize: 'clamp(2rem, 4vw, 3rem)',
              color: 'white',
              letterSpacing: '0.02em',
              margin: '0 0 12px',
            }}
            allowedFormats={[]}
          />
          <RichText
            tagName="p"
            value={sectionSubtitle}
            onChange={(value) => setAttributes({ sectionSubtitle: value })}
            placeholder="Section subtitle…"
            style={{ fontSize: '1.125rem', color: 'rgba(255,255,255,0.6)', margin: 0 }}
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        <div
          style={{
            display: 'grid',
            gridTemplateColumns: `repeat(${Math.min(features.length, 4)}, 1fr)`,
            gap: '24px',
          }}
        >
          {features.map((feature, index) => (
            <div
              key={index}
              style={{
                position: 'relative',
                padding: '32px',
                borderRadius: '16px',
                backgroundColor: 'var(--bg-gray-4, #1a1a1a)',
                border: '1px solid var(--border, #2a2a2a)',
                overflow: 'hidden',
              }}
            >
              {/* Icon box */}
              <div
                style={{
                  width: '64px',
                  height: '64px',
                  marginBottom: '24px',
                  borderRadius: '12px',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  backgroundColor: 'var(--bg-dark-2, #111)',
                  border: '2px solid var(--color-primary, #f5b335)',
                  boxShadow: '0 0 20px rgba(245, 179, 53, 0.2)',
                }}
              >
                {feature.svgUrl
                  ? <img src={feature.svgUrl} alt="" style={{ width: '32px', height: '32px', objectFit: 'contain' }} />
                  : <SvgPlaceholder />
                }
              </div>

              <h3 style={{ fontFamily: "'Outfit', sans-serif", fontWeight: 600, fontSize: '1.25rem', color: '#fff', margin: '0 0 12px' }}>
                {feature.title}
              </h3>
              <p style={{ color: 'rgba(255,255,255,0.7)', lineHeight: 1.6, margin: 0 }}>
                {feature.description}
              </p>

              {/* Bottom accent line preview */}
              <div
                style={{
                  position: 'absolute',
                  bottom: 0,
                  left: 0,
                  right: 0,
                  height: '3px',
                  borderRadius: '0 0 16px 16px',
                  backgroundColor: 'var(--color-primary, #f5b335)',
                  opacity: 0.4,
                }}
              />
            </div>
          ))}
        </div>
      </div>
    </>
  );
}
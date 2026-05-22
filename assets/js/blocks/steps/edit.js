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

// Design tokens
const C = {
  bg:      '#1a1025',
  cardBg:  '#1e1530',
  border:  'rgba(140,60,160,0.35)',
  primary: '#e91e8c',
  white:   '#ffffff',
  muted:   'rgba(255,255,255,0.5)',
};

// Placeholder icon shown when no image uploaded
const PlaceholderIcon = () => (
  <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" style={{ opacity: 0.35 }}>
    <rect x="3" y="3" width="18" height="18" rx="2"/>
    <circle cx="8.5" cy="8.5" r="1.5"/>
    <polyline points="21 15 16 10 5 21"/>
  </svg>
);

export default function Edit({ attributes, setAttributes }) {
  const { sectionTitle, sectionSubtitle, footerText, steps } = attributes;

  const updateStep = (index, field, value) => {
    setAttributes({ steps: steps.map((s, i) => i === index ? { ...s, [field]: value } : s) });
  };

  const addStep = () => {
    setAttributes({
      steps: [...steps, {
        number:  String(steps.length + 1),
        iconUrl: '',
        iconAlt: '',
        title:   'New Step',
        description: 'Describe this step.',
      }],
    });
  };

  const removeStep = (index) => setAttributes({ steps: steps.filter((_, i) => i !== index) });

  const moveStep = (index, dir) => {
    const updated = [...steps];
    const target = index + dir;
    if (target < 0 || target >= updated.length) return;
    [updated[index], updated[target]] = [updated[target], updated[index]];
    setAttributes({ steps: updated });
  };

  const blockProps = useBlockProps({
    className: 'steps-editor-wrapper',
    style: { backgroundColor: C.bg, padding: '48px 32px', fontFamily: "'Montserrat', sans-serif" },
  });

  const colCount = Math.min(steps.length, 4);

  return (
    <>
      {/* ── Sidebar ── */}
      <InspectorControls>
        <PanelBody title="Section Heading" initialOpen={true}>
          <TextControl
            label="Title (use <span class='highlight'>…</span> for pink text)"
            value={sectionTitle}
            onChange={(v) => setAttributes({ sectionTitle: v })}
          />
          <TextareaControl
            label="Subtitle"
            value={sectionSubtitle}
            onChange={(v) => setAttributes({ sectionSubtitle: v })}
            rows={2}
          />
          <TextareaControl
            label="Footer Text"
            value={footerText}
            onChange={(v) => setAttributes({ footerText: v })}
            rows={3}
          />
        </PanelBody>


        <PanelBody title={`Steps (${steps.length})`} initialOpen={true}>
          {steps.map((step, index) => (
            <Card key={index} style={{ marginBottom: '12px', border: '1px solid #444' }}>
              <CardHeader>
                <Flex align="center">
                  <FlexItem><strong style={{ color: '#aaa', fontSize: '12px' }}>Step {index + 1}</strong></FlexItem>
                  <FlexBlock />
                  <FlexItem><Button icon={chevronUp}   isSmall disabled={index === 0}                onClick={() => moveStep(index, -1)} label="Move up"   /></FlexItem>
                  <FlexItem><Button icon={chevronDown} isSmall disabled={index === steps.length - 1} onClick={() => moveStep(index,  1)} label="Move down" /></FlexItem>
                  <FlexItem><Button icon={trash} isSmall isDestructive disabled={steps.length <= 1}  onClick={() => removeStep(index)}   label="Remove"    /></FlexItem>
                </Flex>
              </CardHeader>
              <CardBody>
                <TextControl
                  label="Number Label"
                  value={step.number}
                  onChange={(v) => updateStep(index, 'number', v)}
                  placeholder="1"
                />

                {/* Image upload */}
                <div style={{ marginBottom: '16px' }}>
                  <p style={{ marginBottom: '8px', fontSize: '11px', fontWeight: 600, textTransform: 'uppercase', color: '#1e1e1e' }}>Icon Image</p>
                  <MediaUploadCheck>
                    <MediaUpload
                      onSelect={(media) => {
                        const updated = steps.map((s, i) => i === index ? {
                          ...s,
                          iconId:  media.id,
                          iconUrl: media.url,
                          iconAlt: media.alt || '',
                        } : s);
                        setAttributes({ steps: updated });
                      }}
                      allowedTypes={['image']}
                      value={step.iconId || 0}
                      render={({ open }) => (
                        <div>
                          {step.iconUrl ? (
                            <div style={{ marginBottom: '8px' }}>
                              <img
                                src={step.iconUrl}
                                alt={step.iconAlt}
                                style={{ width: '48px', height: '48px', objectFit: 'contain', display: 'block', marginBottom: '6px' }}
                              />
                              <Flex gap={2}>
                                <FlexItem>
                                  <Button isSmall variant="secondary" onClick={open}>Replace</Button>
                                </FlexItem>
                                <FlexItem>
                                  <Button isSmall variant="tertiary" isDestructive onClick={() => {
                                    const updated = steps.map((s, i) => i === index ? { ...s, iconId: 0, iconUrl: '', iconAlt: '' } : s);
                                    setAttributes({ steps: updated });
                                  }}>
                                    Remove
                                  </Button>
                                </FlexItem>
                              </Flex>
                            </div>
                          ) : (
                            <Button isSmall variant="secondary" onClick={open}>Upload Icon</Button>
                          )}
                        </div>
                      )}
                    />
                  </MediaUploadCheck>
                </div>

                <TextControl
                  label="Title"
                  value={step.title}
                  onChange={(v) => updateStep(index, 'title', v)}
                />
                <TextareaControl
                  label="Description"
                  value={step.description}
                  onChange={(v) => updateStep(index, 'description', v)}
                  rows={3}
                />
              </CardBody>
            </Card>
          ))}
          <Button icon={plus} variant="secondary" onClick={addStep} style={{ width: '100%', justifyContent: 'center', marginTop: '4px' }}>
            Add Step
          </Button>
        </PanelBody>
      </InspectorControls>

      {/* ── Editor canvas preview ── */}
      <div {...blockProps}>

        {/* Heading */}
        <div style={{ textAlign: 'center', marginBottom: '48px' }}>
          <RichText
            tagName="h2"
            value={sectionTitle}
            onChange={(v) => setAttributes({ sectionTitle: v })}
            placeholder="Section title…"
            style={{
              fontFamily: "'Montserrat', sans-serif",
              fontSize: 'clamp(1.5rem, 3vw, 2.25rem)',
              fontWeight: 800,
              textTransform: 'uppercase',
              color: C.white,
              letterSpacing: '0.01em',
              margin: '0 0 12px',
            }}
            allowedFormats={['core/bold', 'core/text-color']}
          />
          <RichText
            tagName="p"
            value={sectionSubtitle}
            onChange={(v) => setAttributes({ sectionSubtitle: v })}
            placeholder="Section subtitle…"
            style={{ fontFamily: "'Montserrat', sans-serif", fontSize: '1rem', color: C.muted, margin: 0 }}
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {/* Steps grid */}
        <div style={{ display: 'grid', gridTemplateColumns: `repeat(${colCount}, 1fr)`, gap: '20px' }}>
          {steps.map((step, index) => {
            const isLast = index === steps.length - 1;
            return (
              <div key={index} style={{ display: 'flex', flexDirection: 'column', position: 'relative' }}>

                {/* Badge */}
                <div style={{
                  position: 'relative',
                  zIndex: 1,
                  width: '34px',
                  height: '34px',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  backgroundColor: C.primary,
                  color: '#fff',
                  fontFamily: "'Montserrat', sans-serif",
                  fontSize: '0.8125rem',
                  fontWeight: 700,
                  borderRadius: '6px',
                  flexShrink: 0,
                  alignSelf: 'flex-start',
                }}>
                  {step.number}
                </div>

                {/* Connector line */}
                {!isLast && (
                  <div style={{
                    position: 'absolute',
                    top: '17px',
                    left: '34px',
                    right: '-20px',
                    height: '2px',
                    background: `linear-gradient(to right, ${C.primary}, rgba(233,30,140,0.15))`,
                    zIndex: 0,
                  }} />
                )}

                {/* Card */}
                <div style={{
                  flex: 1,
                  padding: '28px 20px 24px',
                  borderRadius: '12px',
                  backgroundColor: C.cardBg,
                  border: `1px solid ${C.border}`,
                  textAlign: 'center',
                }}>
                  {/* Icon image or placeholder */}
                  <div style={{
                    width: '56px',
                    height: '56px',
                    margin: '0 auto 18px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: C.primary,
                  }}>
                    {step.iconUrl
                      ? <img src={step.iconUrl} alt={step.iconAlt} style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
                      : <PlaceholderIcon />
                    }
                  </div>

                  <h3 style={{
                    fontFamily: "'Montserrat', sans-serif",
                    fontWeight: 700,
                    fontSize: '0.875rem',
                    textTransform: 'uppercase',
                    letterSpacing: '0.1em',
                    color: C.white,
                    margin: '0 0 10px',
                  }}>
                    {step.title}
                  </h3>

                  <p style={{
                    fontFamily: "'Montserrat', sans-serif",
                    fontSize: '0.8125rem',
                    color: C.muted,
                    lineHeight: 1.65,
                    margin: 0,
                  }}>
                    {step.description}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

        {/* Footer text */}
        <div style={{ textAlign: 'center', paddingTop: '40px' }}>
          <RichText
            tagName="p"
            value={footerText}
            onChange={(v) => setAttributes({ footerText: v })}
            placeholder="Footer note…"
            style={{ fontFamily: "'Montserrat', sans-serif", fontSize: '0.9375rem', color: C.muted, margin: 0 }}
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {/* Overlay — pure CSS gradient, no image */}
        <div style={{
          position: 'absolute',
          inset: 0,
          background: 'linear-gradient(90deg, rgba(16, 14, 27, 0.20) 0%, rgba(247, 29, 194, 0) 50%)',
          pointerEvents: 'none',
          zIndex: 100,
        }} />
      </div>
    </>
  );
}
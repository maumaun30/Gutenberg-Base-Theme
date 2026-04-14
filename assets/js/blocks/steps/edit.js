import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
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

export default function Edit({ attributes, setAttributes }) {
  const { sectionTitle, sectionSubtitle, steps } = attributes;

  // ── Repeater helpers ──────────────────────────────────────────────

  const updateStep = (index, field, value) => {
    const updated = steps.map((step, i) =>
      i === index ? { ...step, [field]: value } : step
    );
    setAttributes({ steps: updated });
  };

  const addStep = () => {
    const next = String(steps.length + 1).padStart(2, '0');
    setAttributes({
      steps: [
        ...steps,
        { number: next, title: 'New Step', description: 'Describe this step.' },
      ],
    });
  };

  const removeStep = (index) => {
    setAttributes({ steps: steps.filter((_, i) => i !== index) });
  };

  const moveStep = (index, direction) => {
    const updated = [...steps];
    const target = index + direction;
    if (target < 0 || target >= updated.length) return;
    [updated[index], updated[target]] = [updated[target], updated[index]];
    setAttributes({ steps: updated });
  };

  // ── Block props ───────────────────────────────────────────────────

  const blockProps = useBlockProps({
    className: 'steps-editor-wrapper',
    style: {
      backgroundColor: 'var(--bg-dark-2, #111)',
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

        <PanelBody title={`Steps (${steps.length})`} initialOpen={true}>
          {steps.map((step, index) => (
            <Card
              key={index}
              style={{ marginBottom: '12px', border: '1px solid #444' }}
            >
              <CardHeader>
                <Flex align="center">
                  <FlexItem>
                    <strong style={{ color: '#aaa', fontSize: '12px' }}>
                      Step {index + 1}
                    </strong>
                  </FlexItem>
                  <FlexBlock />
                  <FlexItem>
                    <Button
                      icon={chevronUp}
                      isSmall
                      disabled={index === 0}
                      onClick={() => moveStep(index, -1)}
                      label="Move up"
                    />
                  </FlexItem>
                  <FlexItem>
                    <Button
                      icon={chevronDown}
                      isSmall
                      disabled={index === steps.length - 1}
                      onClick={() => moveStep(index, 1)}
                      label="Move down"
                    />
                  </FlexItem>
                  <FlexItem>
                    <Button
                      icon={trash}
                      isSmall
                      isDestructive
                      disabled={steps.length <= 1}
                      onClick={() => removeStep(index)}
                      label="Remove step"
                    />
                  </FlexItem>
                </Flex>
              </CardHeader>
              <CardBody>
                <TextControl
                  label="Number label"
                  value={step.number}
                  onChange={(value) => updateStep(index, 'number', value)}
                  placeholder="01"
                />
                <TextControl
                  label="Title"
                  value={step.title}
                  onChange={(value) => updateStep(index, 'title', value)}
                />
                <TextareaControl
                  label="Description"
                  value={step.description}
                  onChange={(value) => updateStep(index, 'description', value)}
                  rows={3}
                />
              </CardBody>
            </Card>
          ))}

          <Button
            icon={plus}
            variant="secondary"
            onClick={addStep}
            style={{ width: '100%', justifyContent: 'center', marginTop: '4px' }}
          >
            Add Step
          </Button>
        </PanelBody>
      </InspectorControls>

      {/* ── Editor preview ── */}
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
            style={{
              fontSize: '1.125rem',
              color: 'rgba(255,255,255,0.6)',
              margin: 0,
            }}
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        <div
          style={{
            display: 'grid',
            gridTemplateColumns: `repeat(${Math.min(steps.length, 3)}, 1fr)`,
            gap: '32px',
          }}
        >
          {steps.map((step, index) => (
            <div
              key={index}
              style={{
                position: 'relative',
                padding: '32px',
                borderRadius: '16px',
                backgroundColor: 'var(--bg-dark-4, #1e1e1e)',
                border: '1px solid var(--border, #333)',
              }}
            >
              {/* Connecting line */}
              {index < steps.length - 1 && (
                <div
                  style={{
                    position: 'absolute',
                    top: '64px',
                    left: '100%',
                    width: '32px',
                    height: '2px',
                    background: 'linear-gradient(to right, var(--color-primary, #f5b335), transparent)',
                  }}
                />
              )}

              <div
                style={{
                  display: 'inline-block',
                  marginBottom: '24px',
                  padding: '12px 24px',
                  borderRadius: '12px',
                  fontFamily: "'Bebas Neue', sans-serif",
                  fontSize: '1.5rem',
                  fontWeight: 700,
                  backgroundColor: 'var(--bg-dark-1, #0a0a0b)',
                  color: 'var(--color-primary, #f5b335)',
                  border: '2px solid var(--color-primary, #f5b335)',
                }}
              >
                {step.number}
              </div>

              <h3
                style={{
                  fontFamily: "'Outfit', sans-serif",
                  fontWeight: 600,
                  fontSize: '1.375rem',
                  color: 'white',
                  margin: '0 0 12px',
                }}
              >
                {step.title}
              </h3>
              <p style={{ color: 'rgba(255,255,255,0.7)', lineHeight: 1.6, margin: 0 }}>
                {step.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </>
  );
}
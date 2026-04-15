import {
  RichText,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
} from '@wordpress/components';
import './style.css';

export default function Edit({ attributes, setAttributes }) {
  const { heading, paragraphs, stats } = attributes;

  function updateParagraph(index, value) {
    const next = [...paragraphs];
    next[index] = value;
    setAttributes({ paragraphs: next });
  }

  function addParagraph() {
    setAttributes({ paragraphs: [...paragraphs, 'New paragraph text.'] });
  }

  function removeParagraph(index) {
    setAttributes({ paragraphs: paragraphs.filter((_, i) => i !== index) });
  }

  function updateStat(index, key, value) {
    const next = stats.map((s, i) => (i === index ? { ...s, [key]: value } : s));
    setAttributes({ stats: next });
  }

  function addStat() {
    setAttributes({ stats: [...stats, { value: '0', label: 'Label' }] });
  }

  function removeStat(index) {
    setAttributes({ stats: stats.filter((_, i) => i !== index) });
  }

  return (
    <>
      <InspectorControls>
        <PanelBody title="Paragraphs" initialOpen={true}>
          {paragraphs.map((p, i) => (
            <div key={i} style={{ marginBottom: '12px' }}>
              <TextControl
                label={`Paragraph ${i + 1}`}
                value={p}
                onChange={(v) => updateParagraph(i, v)}
              />
              <Button
                isDestructive
                variant="tertiary"
                onClick={() => removeParagraph(i)}
              >
                Remove
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addParagraph}>
            + Add Paragraph
          </Button>
        </PanelBody>

        <PanelBody title="Stats" initialOpen={false}>
          {stats.map((stat, i) => (
            <div
              key={i}
              style={{
                marginBottom: '12px',
                paddingBottom: '12px',
                borderBottom: '1px solid #444',
              }}
            >
              <TextControl
                label="Value"
                value={stat.value}
                onChange={(v) => updateStat(i, 'value', v)}
              />
              <TextControl
                label="Label"
                value={stat.label}
                onChange={(v) => updateStat(i, 'label', v)}
              />
              <Button
                isDestructive
                variant="tertiary"
                onClick={() => removeStat(i)}
              >
                Remove
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addStat}>
            + Add Stat
          </Button>
        </PanelBody>
      </InspectorControls>

      <section
        {...useBlockProps({
          className: 'discover-block bg-dark-3 section',
        })}
      >
        <div className="discover-inner">
          <RichText
            tagName="h2"
            className="discover-heading"
            value={heading}
            onChange={(v) => setAttributes({ heading: v })}
            placeholder="Enter heading…"
          />

          <div className="discover-body">
            {paragraphs.map((p, i) => (
              <RichText
                key={i}
                tagName="p"
                value={p}
                onChange={(v) => updateParagraph(i, v)}
                placeholder="Enter paragraph…"
              />
            ))}
          </div>

          <div className="discover-stats">
            {stats.map((stat, i) => (
              <div key={i} className="discover-stat">
                <span className="discover-stat__value">{stat.value}</span>
                <span className="discover-stat__label">{stat.label}</span>
              </div>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
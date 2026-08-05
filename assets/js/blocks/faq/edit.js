import {
  useBlockProps,
  RichText,
  InspectorControls,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
  TextareaControl,
  ToggleControl,
  Placeholder,
} from '@wordpress/components';

const EMPTY_ITEM = {
  question: '',
  answer: '',
};

export default function Edit({ attributes, setAttributes }) {
  const { title, titleHighlight, subtitle, isPageTitle, items, singleOpen } = attributes;

  const list = items || [];

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  const addItem = () => setAttributes({ items: [...list, { ...EMPTY_ITEM }] });

  const updateItem = (index, changes) => {
    setAttributes({
      items: list.map((item, i) => (i === index ? { ...item, ...changes } : item)),
    });
  };

  const removeItem = (index) => setAttributes({ items: list.filter((_, i) => i !== index) });

  const moveItem = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ items: next });
  };

  const blockProps = useBlockProps({ className: 'faq-editor' });

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
          <ToggleControl
            label="Open One at a Time"
            help="Opening an answer closes the others."
            checked={!!singleOpen}
            onChange={(value) => setAttributes({ singleOpen: value })}
          />
          <TextareaControl
            label="Description / Subtext"
            help="Optional. Hidden on the front end when left empty."
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
          />
        </PanelBody>

        {list.map((item, index) => (
          <PanelBody key={index} title={`Q${index + 1}: ${item.question || ''}`} initialOpen={false}>
            <TextControl
              label="Question"
              value={item.question}
              onChange={(value) => updateItem(index, { question: value })}
            />
            <TextareaControl
              label="Answer"
              value={item.answer}
              onChange={(value) => updateItem(index, { answer: value })}
            />

            <div style={{ display: 'flex', gap: '8px' }}>
              <Button variant="secondary" size="small" onClick={() => moveItem(index, -1)} disabled={index === 0}>
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveItem(index, 1)}
                disabled={index === list.length - 1}
              >
                Move Down
              </Button>
              <Button variant="tertiary" isDestructive size="small" onClick={() => removeItem(index)}>
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Question" initialOpen={true}>
          <Button variant="primary" onClick={addItem}>
            Add Question
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="faq-editor__header">
          <TitleTag className="faq-editor__title">
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
            className="faq-editor__subtitle"
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
            placeholder="Optional subtext — leave empty to hide it on the front end."
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {list.length ? (
          <>
            {/* Every answer is shown expanded in the editor — collapsing them
                would make the text unreachable for editing. */}
            <div className="faq-editor__list">
              {list.map((item, index) => (
                <div key={index} className="faq-editor__item">
                  <div className="faq-editor__row">
                    <RichText
                      tagName="span"
                      className="faq-editor__question"
                      value={item.question}
                      onChange={(value) => updateItem(index, { question: value })}
                      placeholder="Question…"
                      allowedFormats={[]}
                    />
                    <span className="faq-editor__mark" aria-hidden="true">+</span>
                  </div>

                  <RichText
                    tagName="p"
                    className="faq-editor__answer"
                    value={item.answer}
                    onChange={(value) => updateItem(index, { answer: value })}
                    placeholder="Answer…"
                    allowedFormats={['core/bold', 'core/italic', 'core/link']}
                  />

                  <Button
                    variant="tertiary"
                    isDestructive
                    size="small"
                    onClick={() => removeItem(index)}
                  >
                    Remove Question
                  </Button>
                </div>
              ))}
            </div>

            <div className="faq-editor__add">
              <Button variant="secondary" onClick={addItem}>
                Add Question
              </Button>
            </div>
          </>
        ) : (
          <Placeholder
            className="faq-editor__placeholder"
            label="FAQ"
            instructions="No questions yet. Add your first question to start filling in the content."
          >
            <Button variant="primary" onClick={addItem}>
              Add Question
            </Button>
          </Placeholder>
        )}
      </div>
    </>
  );
}

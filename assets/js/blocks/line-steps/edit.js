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

const EMPTY_STEP = {
  title: '',
  description: '',
  number: '',
};

export default function Edit({ attributes, setAttributes }) {
  const { title, titleHighlight, subtitle, isPageTitle, steps, padNumbers, showLine } = attributes;

  const list = steps || [];

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  // Numbers follow the step's position unless the step overrides it.
  const numberFor = (step, index) => {
    if (step.number) {
      return step.number;
    }
    const n = index + 1;
    return padNumbers && n < 10 ? `0${n}` : String(n);
  };

  const addStep = () => setAttributes({ steps: [...list, { ...EMPTY_STEP }] });

  const updateStep = (index, changes) => {
    setAttributes({
      steps: list.map((step, i) => (i === index ? { ...step, ...changes } : step)),
    });
  };

  const removeStep = (index) => setAttributes({ steps: list.filter((_, i) => i !== index) });

  const moveStep = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ steps: next });
  };

  const blockProps = useBlockProps({
    className: `line-steps-editor${showLine ? '' : ' line-steps-editor--no-line'}`,
  });

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
            label="Show Connecting Line"
            checked={!!showLine}
            onChange={(value) => setAttributes({ showLine: value })}
          />
          <ToggleControl
            label="Pad Numbers With Zero"
            help="01, 02, 03… instead of 1, 2, 3."
            checked={!!padNumbers}
            onChange={(value) => setAttributes({ padNumbers: value })}
          />
          <TextareaControl
            label="Description / Subtext"
            help="Optional. Hidden on the front end when left empty."
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
          />
        </PanelBody>

        {list.map((step, index) => (
          <PanelBody key={index} title={`Step ${numberFor(step, index)}: ${step.title || ''}`} initialOpen={false}>
            <TextControl
              label="Step Title"
              value={step.title}
              onChange={(value) => updateStep(index, { title: value })}
            />
            <TextareaControl
              label="Step Description"
              value={step.description}
              onChange={(value) => updateStep(index, { description: value })}
            />
            <TextControl
              label="Number Override"
              help="Leave empty to number automatically by position."
              value={step.number}
              onChange={(value) => updateStep(index, { number: value })}
            />

            <div style={{ display: 'flex', gap: '8px' }}>
              <Button variant="secondary" size="small" onClick={() => moveStep(index, -1)} disabled={index === 0}>
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveStep(index, 1)}
                disabled={index === list.length - 1}
              >
                Move Down
              </Button>
              <Button variant="tertiary" isDestructive size="small" onClick={() => removeStep(index)}>
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Step" initialOpen={true}>
          <Button variant="primary" onClick={addStep}>
            Add Step
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="line-steps-editor__header">
          <TitleTag className="line-steps-editor__title">
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
            className="line-steps-editor__subtitle"
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
            placeholder="Optional subtext — leave empty to hide it on the front end."
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {list.length ? (
          <>
            <ol className="line-steps-editor__list">
              {list.map((step, index) => (
                <li key={index} className="line-steps-editor__step">
                  <span className="line-steps-editor__marker">
                    <span className="line-steps-editor__number">{numberFor(step, index)}</span>
                  </span>

                  <RichText
                    tagName="h3"
                    className="line-steps-editor__step-title"
                    value={step.title}
                    onChange={(value) => updateStep(index, { title: value })}
                    placeholder="Step title…"
                    allowedFormats={[]}
                  />
                  <RichText
                    tagName="p"
                    className="line-steps-editor__step-text"
                    value={step.description}
                    onChange={(value) => updateStep(index, { description: value })}
                    placeholder="Step description…"
                    allowedFormats={['core/bold', 'core/italic']}
                  />

                  <Button
                    variant="tertiary"
                    isDestructive
                    size="small"
                    onClick={() => removeStep(index)}
                  >
                    Remove Step
                  </Button>
                </li>
              ))}
            </ol>

            <div className="line-steps-editor__add">
              <Button variant="secondary" onClick={addStep}>
                Add Step
              </Button>
            </div>
          </>
        ) : (
          <Placeholder
            className="line-steps-editor__placeholder"
            label="Line Steps"
            instructions="No steps yet. Add your first step to start filling in the content."
          >
            <Button variant="primary" onClick={addStep}>
              Add Step
            </Button>
          </Placeholder>
        )}
      </div>
    </>
  );
}

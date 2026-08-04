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
  TextareaControl,
  ToggleControl,
  RangeControl,
  Placeholder,
} from '@wordpress/components';

const EMPTY_CARD = {
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
  title: 'Card Title',
  description: '',
};

export default function Edit({ attributes, setAttributes }) {
  const { title, titleHighlight, subtitle, isPageTitle, cards, columns } = attributes;

  const list = cards || [];

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  const addCard = () => setAttributes({ cards: [...list, { ...EMPTY_CARD }] });

  const updateCard = (index, changes) => {
    setAttributes({
      cards: list.map((card, i) => (i === index ? { ...card, ...changes } : card)),
    });
  };

  const removeCard = (index) => {
    setAttributes({ cards: list.filter((_, i) => i !== index) });
  };

  const moveCard = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ cards: next });
  };

  const blockProps = useBlockProps({ className: 'grid-card-editor' });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Section" initialOpen={true}>
          <RangeControl
            label="Columns (desktop)"
            value={columns}
            onChange={(value) => setAttributes({ columns: value })}
            min={1}
            max={4}
          />
          <ToggleControl
            label="Use as page title (H1)"
            help="Turn on only when this section heading is the main heading of the page."
            checked={!!isPageTitle}
            onChange={(value) => setAttributes({ isPageTitle: value })}
          />
          <TextareaControl
            label="Description / Subtitle"
            help="Optional. Hidden on the front end when left empty."
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
          />
        </PanelBody>

        {list.map((card, index) => (
          <PanelBody key={index} title={`Card ${index + 1}: ${card.title || ''}`} initialOpen={false}>
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateCard(index, {
                    iconUrl: media.url,
                    iconId: media.id,
                    iconAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={card.iconId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    {card.iconUrl ? (
                      <>
                        <img
                          src={card.iconUrl}
                          alt={card.iconAlt}
                          style={{ width: '48px', height: '48px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" size="small" onClick={open}>
                            Replace Icon
                          </Button>
                          <Button
                            variant="tertiary"
                            isDestructive
                            size="small"
                            onClick={() => updateCard(index, { iconUrl: '', iconId: 0, iconAlt: '' })}
                          >
                            Remove
                          </Button>
                        </div>
                      </>
                    ) : (
                      <Button variant="primary" onClick={open}>
                        Upload Icon
                      </Button>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>

            <TextControl
              label="Card Title"
              value={card.title}
              onChange={(value) => updateCard(index, { title: value })}
            />
            <TextareaControl
              label="Card Description"
              value={card.description}
              onChange={(value) => updateCard(index, { description: value })}
            />

            <div style={{ display: 'flex', gap: '8px' }}>
              <Button variant="secondary" size="small" onClick={() => moveCard(index, -1)} disabled={index === 0}>
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveCard(index, 1)}
                disabled={index === list.length - 1}
              >
                Move Down
              </Button>
              <Button variant="tertiary" isDestructive size="small" onClick={() => removeCard(index)}>
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Card" initialOpen={true}>
          <Button variant="primary" onClick={addCard}>
            Add Card
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="grid-card-editor__header">
          <TitleTag className="grid-card-editor__title">
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

          {/* Placeholder text keeps the field reachable in the editor even while
              it is empty; render.php drops it entirely on the front end. */}
          <RichText
            tagName="p"
            className="grid-card-editor__subtitle"
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
            placeholder="Optional description — leave empty to hide it on the front end."
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {list.length ? (
          <div className="grid-card-editor__grid" style={{ '--grid-card-columns': columns }}>
            {list.map((card, index) => (
              <div key={index} className="grid-card-editor__card">
                <MediaUploadCheck>
                  <MediaUpload
                    onSelect={(media) =>
                      updateCard(index, {
                        iconUrl: media.url,
                        iconId: media.id,
                        iconAlt: media.alt || '',
                      })
                    }
                    allowedTypes={['image']}
                    value={card.iconId}
                    render={({ open }) => (
                      <button type="button" className="grid-card-editor__icon" onClick={open}>
                        {card.iconUrl ? (
                          <img src={card.iconUrl} alt={card.iconAlt} />
                        ) : (
                          <span className="grid-card-editor__icon-placeholder">+</span>
                        )}
                      </button>
                    )}
                  />
                </MediaUploadCheck>

                <RichText
                  tagName="h3"
                  className="grid-card-editor__card-title"
                  value={card.title}
                  onChange={(value) => updateCard(index, { title: value })}
                  placeholder="Card title…"
                  allowedFormats={[]}
                />
                <RichText
                  tagName="p"
                  className="grid-card-editor__card-text"
                  value={card.description}
                  onChange={(value) => updateCard(index, { description: value })}
                  placeholder="Card description…"
                  allowedFormats={['core/bold', 'core/italic']}
                />

                <Button
                  variant="tertiary"
                  isDestructive
                  size="small"
                  className="grid-card-editor__card-remove"
                  onClick={() => removeCard(index)}
                >
                  Remove Card
                </Button>
              </div>
            ))}
          </div>
        ) : (
          <Placeholder
            className="grid-card-editor__placeholder"
            label="Grid Card"
            instructions="No cards yet. Add your first card to start filling in the content."
          >
            <Button variant="primary" onClick={addCard}>
              Add Card
            </Button>
          </Placeholder>
        )}

        {!!list.length && (
          <div className="grid-card-editor__add">
            <Button variant="secondary" onClick={addCard}>
              Add Card
            </Button>
          </div>
        )}
      </div>
    </>
  );
}

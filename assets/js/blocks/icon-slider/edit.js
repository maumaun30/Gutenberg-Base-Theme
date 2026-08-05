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
  SelectControl,
  Placeholder,
} from '@wordpress/components';

const EMPTY_ITEM = {
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
  label: '',
};

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    titleHighlight,
    subtitle,
    isPageTitle,
    items,
    speed,
    direction,
    pauseOnHover,
  } = attributes;

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

  const blockProps = useBlockProps({ className: 'icon-slider-editor' });

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
          <TextareaControl
            label="Description / Subtext"
            help="Optional. Hidden on the front end when left empty."
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
          />
        </PanelBody>

        <PanelBody title="Motion" initialOpen={false}>
          <SelectControl
            label="Direction"
            value={direction}
            options={[
              { label: 'Right to left', value: 'left' },
              { label: 'Left to right', value: 'right' },
            ]}
            onChange={(value) => setAttributes({ direction: value })}
          />
          <RangeControl
            label="Seconds per item"
            help="Total loop time scales with the number of items, so the speed stays constant as you add more."
            value={speed}
            onChange={(value) => setAttributes({ speed: value })}
            min={1}
            max={15}
          />
          <ToggleControl
            label="Pause on Hover"
            checked={!!pauseOnHover}
            onChange={(value) => setAttributes({ pauseOnHover: value })}
          />
        </PanelBody>

        {list.map((item, index) => (
          <PanelBody key={index} title={`Item ${index + 1}: ${item.label || ''}`} initialOpen={false}>
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateItem(index, {
                    iconUrl: media.url,
                    iconId: media.id,
                    iconAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={item.iconId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    {item.iconUrl ? (
                      <>
                        <img
                          src={item.iconUrl}
                          alt={item.iconAlt}
                          style={{ width: '36px', height: '36px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" size="small" onClick={open}>
                            Replace Icon
                          </Button>
                          <Button
                            variant="tertiary"
                            isDestructive
                            size="small"
                            onClick={() => updateItem(index, { iconUrl: '', iconId: 0, iconAlt: '' })}
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
              label="Label"
              value={item.label}
              onChange={(value) => updateItem(index, { label: value })}
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

        <PanelBody title="Add Item" initialOpen={true}>
          <Button variant="primary" onClick={addItem}>
            Add Item
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="icon-slider-editor__header">
          <TitleTag className="icon-slider-editor__title">
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
            className="icon-slider-editor__subtitle"
            value={subtitle}
            onChange={(value) => setAttributes({ subtitle: value })}
            placeholder="Optional subtext — leave empty to hide it on the front end."
            allowedFormats={['core/bold', 'core/italic']}
          />
        </div>

        {list.length ? (
          <>
            {/* Static preview — the marquee only animates on the front end so it
                doesn't fight with editing. */}
            <div className="icon-slider-editor__row">
              {list.map((item, index) => (
                <span key={index} className="icon-slider-editor__pill">
                  <MediaUploadCheck>
                    <MediaUpload
                      onSelect={(media) =>
                        updateItem(index, {
                          iconUrl: media.url,
                          iconId: media.id,
                          iconAlt: media.alt || '',
                        })
                      }
                      allowedTypes={['image']}
                      value={item.iconId}
                      render={({ open }) => (
                        <button type="button" className="icon-slider-editor__icon" onClick={open}>
                          {item.iconUrl ? (
                            <img src={item.iconUrl} alt={item.iconAlt} />
                          ) : (
                            <span className="icon-slider-editor__icon-placeholder">+</span>
                          )}
                        </button>
                      )}
                    />
                  </MediaUploadCheck>

                  <RichText
                    tagName="span"
                    className="icon-slider-editor__label"
                    value={item.label}
                    onChange={(value) => updateItem(index, { label: value })}
                    placeholder="Label…"
                    allowedFormats={[]}
                  />
                </span>
              ))}
            </div>

            <div className="icon-slider-editor__add">
              <Button variant="secondary" onClick={addItem}>
                Add Item
              </Button>
            </div>
          </>
        ) : (
          <Placeholder
            className="icon-slider-editor__placeholder"
            label="Icon Slider"
            instructions="No items yet. Add your first item to start filling in the content."
          >
            <Button variant="primary" onClick={addItem}>
              Add Item
            </Button>
          </Placeholder>
        )}
      </div>
    </>
  );
}

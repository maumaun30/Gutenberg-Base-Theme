import {
  useBlockProps,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
  RichText,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
  ToggleControl,
  RangeControl,
} from '@wordpress/components';

const EMPTY_ITEM = {
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
  value: '0',
  label: 'New Stat',
  animate: true,
};

// Items saved before the per-item toggle existed have no `animate` key, so an
// undefined value has to read as "on".
const itemAnimates = (item) => item.animate !== false;

export default function Edit({ attributes, setAttributes }) {
  const { items, animate, duration, columns, showBorder } = attributes;

  const list = items || [];

  const updateItem = (index, changes) => {
    const next = list.map((item, i) => (i === index ? { ...item, ...changes } : item));
    setAttributes({ items: next });
  };

  const removeItem = (index) => {
    setAttributes({ items: list.filter((_, i) => i !== index) });
  };

  const moveItem = (index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ items: next });
  };

  const blockProps = useBlockProps({
    className: `counter-editor${showBorder ? ' counter-editor--bordered' : ''}`,
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Layout" initialOpen={true}>
          <RangeControl
            label="Columns (desktop)"
            value={columns}
            onChange={(value) => setAttributes({ columns: value })}
            min={2}
            max={6}
          />
          <ToggleControl
            label="Show Border"
            checked={!!showBorder}
            onChange={(value) => setAttributes({ showBorder: value })}
          />
        </PanelBody>

        <PanelBody title="Count Animation" initialOpen={false}>
          <ToggleControl
            label="Animate Counting"
            help="Counts up from zero when the block scrolls into view. Any prefix or suffix in the value (M+, /7, ★) is kept as-is. Individual items can still opt out in their own panel."
            checked={!!animate}
            onChange={(value) => setAttributes({ animate: value })}
          />
          {animate && (
            <RangeControl
              label="Duration (ms)"
              value={duration}
              onChange={(value) => setAttributes({ duration: value })}
              min={300}
              max={5000}
              step={100}
            />
          )}
        </PanelBody>

        {list.map((item, index) => (
          <PanelBody
            key={index}
            title={`Item ${index + 1}: ${item.label || ''}${itemAnimates(item) ? '' : ' (static)'}`}
            initialOpen={false}
          >
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
                          style={{ width: '48px', height: '48px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" onClick={open} size="small">
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
              label="Value"
              help="e.g. 1M+, 100+, 4.8, 24/7"
              value={item.value}
              onChange={(value) => updateItem(index, { value })}
            />
            <TextControl
              label="Label"
              value={item.label}
              onChange={(label) => updateItem(index, { label })}
            />

            <ToggleControl
              label="Animate This Item"
              help={
                animate
                  ? 'Turn off to show this value straight away while the other items count up.'
                  : 'Counting is off for the whole block, so this item stays static regardless.'
              }
              checked={itemAnimates(item)}
              onChange={(value) => updateItem(index, { animate: value })}
              disabled={!animate}
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
          <Button variant="primary" onClick={() => setAttributes({ items: [...list, { ...EMPTY_ITEM }] })}>
            Add Counter Item
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div
          className="counter-editor__grid"
          style={{ '--counter-columns': columns }}
        >
          {list.map((item, index) => (
            <div key={index} className="counter-editor__item">
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
                    <button type="button" className="counter-editor__icon" onClick={open}>
                      {item.iconUrl ? (
                        <img src={item.iconUrl} alt={item.iconAlt} />
                      ) : (
                        <span className="counter-editor__icon-placeholder">+</span>
                      )}
                    </button>
                  )}
                />
              </MediaUploadCheck>

              <RichText
                tagName="div"
                className="counter-editor__value"
                value={item.value}
                onChange={(value) => updateItem(index, { value })}
                placeholder="0"
                allowedFormats={[]}
              />
              <RichText
                tagName="div"
                className="counter-editor__label"
                value={item.label}
                onChange={(label) => updateItem(index, { label })}
                placeholder="Label…"
                allowedFormats={[]}
              />
            </div>
          ))}

          {!list.length && (
            <p className="counter-editor__empty">
              No counter items yet — add one from the block settings sidebar.
            </p>
          )}
        </div>
      </div>
    </>
  );
}

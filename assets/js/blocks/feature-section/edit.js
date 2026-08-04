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
  SelectControl,
} from '@wordpress/components';

const EMPTY_ITEM = {
  title: '',
  description: '',
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
};

const EMPTY_BUTTON = {
  iconUrl: '',
  iconId: 0,
  iconAlt: '',
  smallText: '',
  mainText: 'Button',
  action: 'link',
  url: '',
  fileUrl: '',
  fileId: 0,
  fileName: '',
  newTab: false,
};

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    titleHighlight,
    subtitle,
    isPageTitle,
    imageUrl,
    imageId,
    imageAlt,
    items,
    buttons,
    mediaPosition,
  } = attributes;

  const itemList = items || [];
  const buttonList = buttons || [];

  // Capitalized so JSX treats it as the element type.
  const TitleTag = isPageTitle ? 'h1' : 'h2';

  const updateList = (key, list, index, changes) => {
    setAttributes({
      [key]: list.map((entry, i) => (i === index ? { ...entry, ...changes } : entry)),
    });
  };

  const removeFrom = (key, list, index) => {
    setAttributes({ [key]: list.filter((_, i) => i !== index) });
  };

  const moveIn = (key, list, index, offset) => {
    const target = index + offset;
    if (target < 0 || target >= list.length) {
      return;
    }
    const next = [...list];
    [next[index], next[target]] = [next[target], next[index]];
    setAttributes({ [key]: next });
  };

  const blockProps = useBlockProps({
    className: `feature-section-editor feature-section-editor--media-${mediaPosition}`,
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Section" initialOpen={true}>
          <SelectControl
            label="Image Position (desktop)"
            value={mediaPosition}
            options={[
              { label: 'Right', value: 'right' },
              { label: 'Left', value: 'left' },
            ]}
            onChange={(value) => setAttributes({ mediaPosition: value })}
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

        <PanelBody title="Image" initialOpen={false}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={(media) =>
                setAttributes({
                  imageUrl: media.url,
                  imageId: media.id,
                  imageAlt: media.alt || '',
                })
              }
              allowedTypes={['image']}
              value={imageId}
              render={({ open }) => (
                <div>
                  {imageUrl ? (
                    <>
                      <img src={imageUrl} alt={imageAlt} style={{ width: '100%', display: 'block' }} />
                      <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                        <Button variant="secondary" size="small" onClick={open}>
                          Replace Image
                        </Button>
                        <Button
                          variant="tertiary"
                          isDestructive
                          size="small"
                          onClick={() => setAttributes({ imageUrl: '', imageId: 0, imageAlt: '' })}
                        >
                          Remove
                        </Button>
                      </div>
                    </>
                  ) : (
                    <Button variant="primary" onClick={open}>
                      Select Image
                    </Button>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>
        </PanelBody>

        {itemList.map((item, index) => (
          <PanelBody key={`item-${index}`} title={`List Item ${index + 1}: ${item.title || ''}`} initialOpen={false}>
            <TextControl
              label="Item Title"
              value={item.title}
              onChange={(value) => updateList('items', itemList, index, { title: value })}
            />
            <TextareaControl
              label="Item Description"
              value={item.description}
              onChange={(value) => updateList('items', itemList, index, { description: value })}
            />

            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateList('items', itemList, index, {
                    iconUrl: media.url,
                    iconId: media.id,
                    iconAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={item.iconId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    <p style={{ margin: '0 0 4px', fontSize: '11px', textTransform: 'uppercase' }}>
                      Bullet Icon (optional)
                    </p>
                    {item.iconUrl ? (
                      <>
                        <img
                          src={item.iconUrl}
                          alt={item.iconAlt}
                          style={{ width: '28px', height: '28px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" size="small" onClick={open}>
                            Replace
                          </Button>
                          <Button
                            variant="tertiary"
                            isDestructive
                            size="small"
                            onClick={() =>
                              updateList('items', itemList, index, { iconUrl: '', iconId: 0, iconAlt: '' })
                            }
                          >
                            Remove
                          </Button>
                        </div>
                      </>
                    ) : (
                      <Button variant="secondary" size="small" onClick={open}>
                        Upload Icon
                      </Button>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>

            <div style={{ display: 'flex', gap: '8px' }}>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveIn('items', itemList, index, -1)}
                disabled={index === 0}
              >
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveIn('items', itemList, index, 1)}
                disabled={index === itemList.length - 1}
              >
                Move Down
              </Button>
              <Button
                variant="tertiary"
                isDestructive
                size="small"
                onClick={() => removeFrom('items', itemList, index)}
              >
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add List Item" initialOpen={true}>
          <Button variant="primary" onClick={() => setAttributes({ items: [...itemList, { ...EMPTY_ITEM }] })}>
            Add List Item
          </Button>
        </PanelBody>

        {buttonList.map((button, index) => (
          <PanelBody
            key={`button-${index}`}
            title={`Button ${index + 1}: ${button.mainText || ''}`}
            initialOpen={false}
          >
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  updateList('buttons', buttonList, index, {
                    iconUrl: media.url,
                    iconId: media.id,
                    iconAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={button.iconId}
                render={({ open }) => (
                  <div style={{ marginBottom: '12px' }}>
                    <p style={{ margin: '0 0 4px', fontSize: '11px', textTransform: 'uppercase' }}>Button Icon</p>
                    {button.iconUrl ? (
                      <>
                        <img
                          src={button.iconUrl}
                          alt={button.iconAlt}
                          style={{ width: '36px', height: '36px', objectFit: 'contain', display: 'block' }}
                        />
                        <div style={{ marginTop: '8px', display: 'flex', gap: '8px' }}>
                          <Button variant="secondary" size="small" onClick={open}>
                            Replace
                          </Button>
                          <Button
                            variant="tertiary"
                            isDestructive
                            size="small"
                            onClick={() =>
                              updateList('buttons', buttonList, index, { iconUrl: '', iconId: 0, iconAlt: '' })
                            }
                          >
                            Remove
                          </Button>
                        </div>
                      </>
                    ) : (
                      <Button variant="secondary" size="small" onClick={open}>
                        Upload Icon
                      </Button>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>

            <TextControl
              label="Small Text (top line)"
              help="Optional, e.g. “Available on the”."
              value={button.smallText}
              onChange={(value) => updateList('buttons', buttonList, index, { smallText: value })}
            />
            <TextControl
              label="Main Text (bottom line)"
              value={button.mainText}
              onChange={(value) => updateList('buttons', buttonList, index, { mainText: value })}
            />

            <SelectControl
              label="Action"
              value={button.action || 'link'}
              options={[
                { label: 'Go to link', value: 'link' },
                { label: 'Download file (APK, PDF…)', value: 'file' },
              ]}
              onChange={(value) => updateList('buttons', buttonList, index, { action: value })}
            />

            {(button.action || 'link') === 'link' ? (
              <>
                <TextControl
                  label="URL"
                  value={button.url}
                  type="url"
                  onChange={(value) => updateList('buttons', buttonList, index, { url: value })}
                />
                <ToggleControl
                  label="Open in New Tab"
                  checked={!!button.newTab}
                  onChange={(value) => updateList('buttons', buttonList, index, { newTab: value })}
                />
              </>
            ) : (
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(media) =>
                    updateList('buttons', buttonList, index, {
                      fileUrl: media.url,
                      fileId: media.id,
                      fileName: media.filename || media.title || '',
                    })
                  }
                  allowedTypes={[]}
                  value={button.fileId}
                  render={({ open }) => (
                    <div>
                      {button.fileUrl ? (
                        <>
                          <p style={{ margin: '0 0 8px', wordBreak: 'break-all', fontSize: '12px' }}>
                            {button.fileName || button.fileUrl}
                          </p>
                          <div style={{ display: 'flex', gap: '8px' }}>
                            <Button variant="secondary" size="small" onClick={open}>
                              Replace File
                            </Button>
                            <Button
                              variant="tertiary"
                              isDestructive
                              size="small"
                              onClick={() =>
                                updateList('buttons', buttonList, index, {
                                  fileUrl: '',
                                  fileId: 0,
                                  fileName: '',
                                })
                              }
                            >
                              Remove
                            </Button>
                          </div>
                        </>
                      ) : (
                        <Button variant="secondary" onClick={open}>
                          Select / Upload File
                        </Button>
                      )}
                      <p style={{ marginTop: '8px', fontSize: '12px', fontStyle: 'italic' }}>
                        APK files may need to be allowed in Media Library uploads.
                      </p>
                    </div>
                  )}
                />
              </MediaUploadCheck>
            )}

            <div style={{ display: 'flex', gap: '8px', marginTop: '12px' }}>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveIn('buttons', buttonList, index, -1)}
                disabled={index === 0}
              >
                Move Up
              </Button>
              <Button
                variant="secondary"
                size="small"
                onClick={() => moveIn('buttons', buttonList, index, 1)}
                disabled={index === buttonList.length - 1}
              >
                Move Down
              </Button>
              <Button
                variant="tertiary"
                isDestructive
                size="small"
                onClick={() => removeFrom('buttons', buttonList, index)}
              >
                Delete
              </Button>
            </div>
          </PanelBody>
        ))}

        <PanelBody title="Add Button" initialOpen={true}>
          <Button
            variant="primary"
            onClick={() => setAttributes({ buttons: [...buttonList, { ...EMPTY_BUTTON }] })}
          >
            Add Button
          </Button>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="feature-section-editor__content">

          <div className="feature-section-editor__col">
            <TitleTag className="feature-section-editor__title">
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

            {/* Placeholder keeps the field reachable while empty; render.php
                drops it entirely on the front end. */}
            <RichText
              tagName="p"
              className="feature-section-editor__subtitle"
              value={subtitle}
              onChange={(value) => setAttributes({ subtitle: value })}
              placeholder="Optional description — leave empty to hide it on the front end."
              allowedFormats={['core/bold', 'core/italic']}
            />

            {!!itemList.length && (
              <ul className="feature-section-editor__list">
                {itemList.map((item, index) => (
                  <li key={index} className="feature-section-editor__item">
                    <span className="feature-section-editor__bullet" aria-hidden="true">
                      {item.iconUrl ? <img src={item.iconUrl} alt="" /> : '✓'}
                    </span>
                    <span className="feature-section-editor__item-body">
                      <RichText
                        tagName="strong"
                        className="feature-section-editor__item-title"
                        value={item.title}
                        onChange={(value) => updateList('items', itemList, index, { title: value })}
                        placeholder="Item title…"
                        allowedFormats={[]}
                      />
                      <RichText
                        tagName="span"
                        className="feature-section-editor__item-text"
                        value={item.description}
                        onChange={(value) => updateList('items', itemList, index, { description: value })}
                        placeholder="Item description…"
                        allowedFormats={['core/bold', 'core/italic']}
                      />
                    </span>
                  </li>
                ))}
              </ul>
            )}

            {!itemList.length && (
              <p className="feature-section-editor__empty">
                No list items yet — add one from the block settings sidebar.
              </p>
            )}

            {!!buttonList.length && (
              <div className="feature-section-editor__buttons">
                {buttonList.map((button, index) => (
                  <span key={index} className="feature-section-editor__btn">
                    {button.iconUrl && (
                      <span className="feature-section-editor__btn-icon">
                        <img src={button.iconUrl} alt="" />
                      </span>
                    )}
                    <span className="feature-section-editor__btn-text">
                      {button.smallText && (
                        <span className="feature-section-editor__btn-small">{button.smallText}</span>
                      )}
                      <span className="feature-section-editor__btn-main">{button.mainText}</span>
                    </span>
                  </span>
                ))}
              </div>
            )}
          </div>

          <div className="feature-section-editor__media">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  setAttributes({
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || '',
                  })
                }
                allowedTypes={['image']}
                value={imageId}
                render={({ open }) => (
                  <button type="button" className="feature-section-editor__media-btn" onClick={open}>
                    {imageUrl ? (
                      <img src={imageUrl} alt={imageAlt} />
                    ) : (
                      <span className="feature-section-editor__media-placeholder">Upload image</span>
                    )}
                  </button>
                )}
              />
            </MediaUploadCheck>
          </div>

        </div>
      </div>
    </>
  );
}

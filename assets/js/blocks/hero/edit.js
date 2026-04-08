import { RichText, URLInputButton, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
  const { title, content, buttonText, buttonUrl } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title="Hero Settings">
          <TextControl
            label="Button Text"
            value={buttonText}
            onChange={(value) => setAttributes({ buttonText: value })}
          />
          <div style={{ marginTop: "12px" }}>
            <label style={{ display: "block", marginBottom: "8px" }}>Button URL</label>
            <URLInputButton
              url={buttonUrl}
              onChange={(value) => setAttributes({ buttonUrl: value })}
            />
          </div>
        </PanelBody>
      </InspectorControls>

      <section {...useBlockProps({ className: 'rounded-2xl bg-gray-100 px-6 py-16' })}>
        <RichText
          tagName="h2"
          className="mb-4 text-4xl font-bold"
          value={title}
          onChange={(value) => setAttributes({ title: value })}
          placeholder="Hero title"
        />
        <RichText
          tagName="p"
          className="mb-6 text-lg text-gray-600"
          value={content}
          onChange={(value) => setAttributes({ content: value })}
          placeholder="Hero content"
        />
        <div className="inline-flex rounded-lg bg-blue-600 px-5 py-3 text-white">
          {buttonText || 'Learn more'}
        </div>
      </section>
    </>
  );
}
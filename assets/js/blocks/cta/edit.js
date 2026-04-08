import { RichText, URLInputButton, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
  const { heading, buttonText, buttonUrl } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title="CTA Settings">
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

      <section {...useBlockProps({ className: 'rounded-2xl bg-gray-900 px-6 py-12 text-white' })}>
        <RichText
          tagName="h3"
          className="mb-4 text-3xl font-bold"
          value={heading}
          onChange={(value) => setAttributes({ heading: value })}
          placeholder="CTA heading"
        />
        <div className="inline-flex rounded-lg bg-white px-5 py-3 text-gray-900">
          {buttonText || 'Contact us'}
        </div>
      </section>
    </>
  );
}
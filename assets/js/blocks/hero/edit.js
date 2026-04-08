import {
  RichText,
  InspectorControls,
  useBlockProps
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  TextareaControl
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { title, description } = attributes;

  return (
    <>
      <InspectorControls>
        <PanelBody title="Hero Settings" initialOpen={true}>
          <TextControl
            label="Title"
            value={title}
            onChange={(value) => setAttributes({ title: value })}
          />
          <TextareaControl
            label="Description"
            value={description}
            onChange={(value) => setAttributes({ description: value })}
          />
        </PanelBody>
      </InspectorControls>

      <section {...useBlockProps({ className: 'card' })}>
        <RichText
          tagName="h2"
          value={title}
          onChange={(value) => setAttributes({ title: value })}
          placeholder="Enter title..."
        />
        <RichText
          tagName="p"
          value={description}
          onChange={(value) => setAttributes({ description: value })}
          placeholder="Enter description..."
        />
      </section>
    </>
  );
}

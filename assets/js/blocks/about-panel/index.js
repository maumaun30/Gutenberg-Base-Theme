import { registerBlockType, rawHandler } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import metadata from './block.json';
import Edit from './edit';
import './style.css';

registerBlockType(metadata.name, {
  ...metadata,
  edit: Edit,
  // Dynamic block, but the inner blocks still have to be serialized so
  // render.php receives them as $content.
  save: () => <InnerBlocks.Content />,

  deprecated: [
    {
      // v1 kept the body in a `content` attribute written by a multiline
      // RichText, which could only ever produce <p>. Convert that HTML into
      // real paragraph/heading blocks so existing panels keep their copy.
      attributes: {
        ...metadata.attributes,
        content: { type: 'string', default: '' },
      },
      save: () => null,
      isEligible: ({ content }) => !!content,
      migrate: ({ content, ...attributes }) => [
        attributes,
        rawHandler({ HTML: content }),
      ],
    },
  ],
});

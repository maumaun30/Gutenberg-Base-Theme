import {
  useBlockProps,
  RichText,
  InspectorControls,
} from '@wordpress/block-editor';
import {
  PanelBody,
  ToggleControl,
  RangeControl,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { heading, content, collapsible, collapsedHeight } = attributes;

  const blockProps = useBlockProps({ className: 'mytheme-about' });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Panel" initialOpen={true}>
          <ToggleControl
            label="Collapse long text"
            help="Shows a Read More button when the copy is taller than the collapsed height."
            checked={!!collapsible}
            onChange={(value) => setAttributes({ collapsible: value })}
          />
          {collapsible && (
            <RangeControl
              label="Collapsed height (lines)"
              value={collapsedHeight}
              onChange={(value) => setAttributes({ collapsedHeight: value })}
              min={3}
              max={20}
              step={0.5}
            />
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {/* The editor renders the panel at full height — collapsing is a
            front-end concern and would only get in the way of editing. */}
        <div className="mytheme-about__wrap">
          <div className="mytheme-about__hd">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <RichText
              tagName="h2"
              value={heading}
              allowedFormats={[]}
              onChange={(value) => setAttributes({ heading: value })}
              placeholder="About the Game"
            />
          </div>

          <RichText
            tagName="div"
            className="mytheme-about__content"
            multiline="p"
            value={content}
            onChange={(value) => setAttributes({ content: value })}
            placeholder="Write the description…"
          />

          {collapsible && (
            <span className="mytheme-about__toggle">Read More</span>
          )}
        </div>
      </div>
    </>
  );
}

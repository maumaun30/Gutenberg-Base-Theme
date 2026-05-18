import {
	InspectorControls,
	useBlockProps,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	Button,
	BaseControl,
} from '@wordpress/components';

/* ── Reusable image upload row ── */
function ImageUploadControl( { label, value, onSelect, onRemove, previewHeight = 28 } ) {
	return (
		<BaseControl label={ label } __nextHasNoMarginBottom>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ ( media ) => onSelect( media.url ) }
					allowedTypes={ [ 'image' ] }
					value={ value }
					render={ ( { open } ) => (
						<div className="fnlmx-media-upload-wrap">
							{ value && (
								<img
									src={ value }
									alt={ label + ' preview' }
									style={ {
										height: previewHeight + 'px',
										marginBottom: '8px',
										display: 'block',
										objectFit: 'contain',
									} }
								/>
							) }
							<Button onClick={ open } variant="secondary" size="small">
								{ value ? 'Replace ' + label : 'Upload ' + label }
							</Button>
							{ value && (
								<Button
									onClick={ onRemove }
									variant="link"
									isDestructive
									size="small"
									style={ { marginLeft: '8px' } }
								>
									Remove
								</Button>
							) }
						</div>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	);
}

/* ── SVG fallbacks ── */
function ShieldSVG() {
	return (
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
			<path fillRule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clipRule="evenodd" />
		</svg>
	);
}

function LightningSVG() {
	return (
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
			<path fillRule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.818a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.845-.143z" clipRule="evenodd" />
		</svg>
	);
}

/* ── Main Edit component ── */
export default function Edit( { attributes, setAttributes } ) {
	const {
		badgeLabel,
		badgeSubLabel,
		ctaText,
		gcashLogoUrl,
		mayaLogoUrl,
		shieldIconUrl,
		lightningIconUrl,
	} = attributes;

	const blockProps = useBlockProps( { className: 'fnlmx-fast-deposit-wrap' } );

	return (
		<>
			<InspectorControls>

				{ /* ── Text fields ── */ }
				<PanelBody title="Badge Text" initialOpen={ true }>
					<TextControl
						label="Badge Label"
						value={ badgeLabel }
						onChange={ ( v ) => setAttributes( { badgeLabel: v } ) }
						__nextHasNoMarginBottom
					/>
					<TextControl
						label="Badge Sub-Label"
						value={ badgeSubLabel }
						onChange={ ( v ) => setAttributes( { badgeSubLabel: v } ) }
						__nextHasNoMarginBottom
					/>
					<TextControl
						label="CTA Text (right side)"
						value={ ctaText }
						onChange={ ( v ) => setAttributes( { ctaText: v } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				{ /* ── All icons/logos in one panel ── */ }
				<PanelBody title="Logos & Icons" initialOpen={ true }>
					<ImageUploadControl
						label="Shield / Badge Icon"
						value={ shieldIconUrl }
						previewHeight={ 32 }
						onSelect={ ( url ) => setAttributes( { shieldIconUrl: url } ) }
						onRemove={ () => setAttributes( { shieldIconUrl: '' } ) }
					/>
					<ImageUploadControl
						label="GCash Logo"
						value={ gcashLogoUrl }
						onSelect={ ( url ) => setAttributes( { gcashLogoUrl: url } ) }
						onRemove={ () => setAttributes( { gcashLogoUrl: '' } ) }
					/>
					<ImageUploadControl
						label="Maya Logo"
						value={ mayaLogoUrl }
						onSelect={ ( url ) => setAttributes( { mayaLogoUrl: url } ) }
						onRemove={ () => setAttributes( { mayaLogoUrl: '' } ) }
					/>
					<ImageUploadControl
						label="Lightning Icon"
						value={ lightningIconUrl }
						previewHeight={ 32 }
						onSelect={ ( url ) => setAttributes( { lightningIconUrl: url } ) }
						onRemove={ () => setAttributes( { lightningIconUrl: '' } ) }
					/>
				</PanelBody>

			</InspectorControls>

			{ /* ── Live editor preview ── */ }
			<div { ...blockProps }>
				<div className="fnlmx-fast-deposit">

					{ /* Left: shield + label */ }
					<div className="fnlmx-fast-deposit__left">
						<div className="fnlmx-fast-deposit__icon">
							{ shieldIconUrl
								? <img src={ shieldIconUrl } alt={ badgeLabel } className="fnlmx-fast-deposit__shield-img" />
								: <ShieldSVG />
							}
						</div>
						<div className="fnlmx-fast-deposit__label">
							<span className="fnlmx-fast-deposit__title">{ badgeLabel }</span>
							<span className="fnlmx-fast-deposit__subtitle">{ badgeSubLabel }</span>
						</div>
					</div>

					<div className="fnlmx-fast-deposit__divider" aria-hidden="true" />

					{ /* Center: logos */ }
					<div className="fnlmx-fast-deposit__logos">
						{ gcashLogoUrl
							? <img src={ gcashLogoUrl } alt="GCash" className="fnlmx-fast-deposit__logo" />
							: <span className="fnlmx-fast-deposit__logo-placeholder">GCash</span>
						}
						{ mayaLogoUrl
							? <img src={ mayaLogoUrl } alt="Maya" className="fnlmx-fast-deposit__logo" />
							: <span className="fnlmx-fast-deposit__logo-placeholder fnlmx-fast-deposit__logo-placeholder--maya">maya</span>
						}
					</div>

					<div className="fnlmx-fast-deposit__divider" aria-hidden="true" />

					{ /* Right: CTA text + lightning */ }
					<div className="fnlmx-fast-deposit__right">
						<span className="fnlmx-fast-deposit__cta-text">{ ctaText }</span>
						<div className="fnlmx-fast-deposit__lightning" aria-hidden="true">
							{ lightningIconUrl
								? <img src={ lightningIconUrl } alt="" className="fnlmx-fast-deposit__lightning-img" />
								: <LightningSVG />
							}
						</div>
					</div>

				</div>
			</div>
		</>
	);
}
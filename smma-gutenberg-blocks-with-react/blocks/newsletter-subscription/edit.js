/**
 * WordPress dependencies
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Edit component for the Newsletter Subscription block.
 * Renders a static preview of the form in the editor.
 * Form submission only works on the front end (dynamic/PHP rendered).
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor element.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const { blockTitle, subheading, buttonLabel, successMessage } = attributes;
	const blockProps = useBlockProps( { className: 'smma-newsletter' } );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Form Settings', 'smma-gutenberg-blocks' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Button Label', 'smma-gutenberg-blocks' ) }
						value={ buttonLabel }
						onChange={ ( val ) =>
							setAttributes( { buttonLabel: val } )
						}
					/>
					<TextControl
						label={ __( 'Success Message', 'smma-gutenberg-blocks' ) }
						value={ successMessage }
						onChange={ ( val ) =>
							setAttributes( { successMessage: val } )
						}
						help={ __(
							'Shown to the user after a successful subscription.',
							'smma-gutenberg-blocks'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="smma-newsletter__inner">
					<div className="smma-newsletter__header">
						<RichText
							tagName="h2"
							className="smma-newsletter__title"
							value={ blockTitle }
							onChange={ ( val ) =>
								setAttributes( { blockTitle: val } )
							}
							placeholder={ __(
								'Newsletter title…',
								'smma-gutenberg-blocks'
							) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="p"
							className="smma-newsletter__subheading"
							value={ subheading }
							onChange={ ( val ) =>
								setAttributes( { subheading: val } )
							}
							placeholder={ __(
								'Add a short subheading…',
								'smma-gutenberg-blocks'
							) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					</div>

					{ /* Static form preview – not interactive in editor */ }
					<div className="smma-newsletter__form smma-newsletter__form--preview">
						<div className="smma-newsletter__row">
							<div className="smma-newsletter__field">
								<label className="smma-newsletter__label">
									{ __( 'First Name', 'smma-gutenberg-blocks' ) }
								</label>
								<input
									type="text"
									className="smma-newsletter__input"
									placeholder={ __( 'Jane', 'smma-gutenberg-blocks' ) }
									disabled
								/>
							</div>
							<div className="smma-newsletter__field">
								<label className="smma-newsletter__label">
									{ __( 'Last Name', 'smma-gutenberg-blocks' ) }
								</label>
								<input
									type="text"
									className="smma-newsletter__input"
									placeholder={ __( 'Smith', 'smma-gutenberg-blocks' ) }
									disabled
								/>
							</div>
						</div>
						<div className="smma-newsletter__field smma-newsletter__field--email">
							<label className="smma-newsletter__label">
								{ __( 'Email Address', 'smma-gutenberg-blocks' ) }
							</label>
							<input
								type="email"
								className="smma-newsletter__input"
								placeholder={ __( 'jane@example.com', 'smma-gutenberg-blocks' ) }
								disabled
							/>
						</div>
						<button
							className="smma-newsletter__button"
							disabled
						>
							{ buttonLabel }
						</button>
					</div>

					<p className="smma-newsletter__editor-note">
						{ __(
							'ⓘ Form submission is active on the front end. Data is stored in the smma_newsletter_subscribers database table.',
							'smma-gutenberg-blocks'
						) }
					</p>
				</div>
			</div>
		</>
	);
};

export default Edit;

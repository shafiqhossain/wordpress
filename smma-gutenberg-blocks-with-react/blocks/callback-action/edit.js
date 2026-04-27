/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Edit component for the Callback Action block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor element.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const { title, description, buttonLabel, buttonUrl } = attributes;
	const blockProps = useBlockProps( { className: 'smma-callback-action' } );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Button Settings', 'smma-gutenberg-blocks' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Button URL', 'smma-gutenberg-blocks' ) }
						value={ buttonUrl }
						onChange={ ( value ) =>
							setAttributes( { buttonUrl: value } )
						}
						help={ __(
							'Enter the URL the button should link to.',
							'smma-gutenberg-blocks'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="smma-callback-action__inner">
					<div className="smma-callback-action__content">
						<RichText
							tagName="h2"
							className="smma-callback-action__title"
							value={ title }
							onChange={ ( value ) =>
								setAttributes( { title: value } )
							}
							placeholder={ __(
								'Enter title…',
								'smma-gutenberg-blocks'
							) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="p"
							className="smma-callback-action__description"
							value={ description }
							onChange={ ( value ) =>
								setAttributes( { description: value } )
							}
							placeholder={ __(
								'Enter description…',
								'smma-gutenberg-blocks'
							) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					</div>
					<div className="smma-callback-action__action">
						<RichText
							tagName="span"
							className="smma-callback-action__button"
							value={ buttonLabel }
							onChange={ ( value ) =>
								setAttributes( { buttonLabel: value } )
							}
							placeholder={ __(
								'Button label…',
								'smma-gutenberg-blocks'
							) }
							allowedFormats={ [] }
						/>
						<p className="smma-callback-action__url-preview">
							{ __( 'Links to:', 'smma-gutenberg-blocks' ) }{ ' ' }
							<code>{ buttonUrl || '#' }</code>
						</p>
					</div>
				</div>
			</div>
		</>
	);
};

export default Edit;

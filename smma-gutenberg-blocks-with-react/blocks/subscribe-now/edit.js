/**
 * WordPress dependencies
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Edit component for the Subscribe Now block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor element.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const { blockTitle, plans } = attributes;
	const blockProps = useBlockProps( { className: 'smma-subscribe-now' } );

	/**
	 * Updates a specific field of a plan by index.
	 *
	 * @param {number} index Plan index.
	 * @param {string} field Field name.
	 * @param {*}      value New value.
	 */
	const updatePlan = ( index, field, value ) => {
		const updated = plans.map( ( p, i ) =>
			i === index ? { ...p, [ field ]: value } : p
		);
		setAttributes( { plans: updated } );
	};

	return (
		<>
			<InspectorControls>
				{ plans.map( ( plan, i ) => (
					<PanelBody
						key={ plan.id }
						title={ `${ plan.name } ${ __( 'Plan Settings', 'smma-gutenberg-blocks' ) }` }
						initialOpen={ i === 1 }
					>
						<TextControl
							label={ __( 'Plan Name', 'smma-gutenberg-blocks' ) }
							value={ plan.name }
							onChange={ ( val ) => updatePlan( i, 'name', val ) }
						/>
						<TextareaControl
							label={ __( 'Description', 'smma-gutenberg-blocks' ) }
							value={ plan.description }
							onChange={ ( val ) => updatePlan( i, 'description', val ) }
							rows={ 3 }
						/>
						<TextControl
							label={ __( 'Button Label', 'smma-gutenberg-blocks' ) }
							value={ plan.buttonLabel }
							onChange={ ( val ) =>
								updatePlan( i, 'buttonLabel', val )
							}
						/>
						<TextControl
							label={ __( 'Redirect URL', 'smma-gutenberg-blocks' ) }
							value={ plan.redirectUrl }
							onChange={ ( val ) =>
								updatePlan( i, 'redirectUrl', val )
							}
							help={ __(
								'Where the "Select" button will redirect.',
								'smma-gutenberg-blocks'
							) }
						/>
						<ToggleControl
							label={ __( 'Highlight this plan (recommended)', 'smma-gutenberg-blocks' ) }
							checked={ plan.highlighted }
							onChange={ ( val ) =>
								updatePlan( i, 'highlighted', val )
							}
						/>
					</PanelBody>
				) ) }
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="h2"
					className="smma-subscribe-now__heading"
					value={ blockTitle }
					onChange={ ( val ) => setAttributes( { blockTitle: val } ) }
					placeholder={ __( 'Block title…', 'smma-gutenberg-blocks' ) }
					allowedFormats={ [] }
				/>
				<div className="smma-subscribe-now__grid">
					{ plans.map( ( plan, i ) => (
						<div
							key={ plan.id }
							className={ `smma-subscribe-now__card${
								plan.highlighted
									? ' smma-subscribe-now__card--highlighted'
									: ''
							}` }
						>
							{ plan.highlighted && (
								<div className="smma-subscribe-now__badge">
									{ __( 'Recommended', 'smma-gutenberg-blocks' ) }
								</div>
							) }
							<RichText
								tagName="h3"
								className="smma-subscribe-now__plan-name"
								value={ plan.name }
								onChange={ ( val ) =>
									updatePlan( i, 'name', val )
								}
								placeholder={ __( 'Plan name…', 'smma-gutenberg-blocks' ) }
								allowedFormats={ [] }
							/>
							<RichText
								tagName="p"
								className="smma-subscribe-now__plan-desc"
								value={ plan.description }
								onChange={ ( val ) =>
									updatePlan( i, 'description', val )
								}
								placeholder={ __( 'Plan description…', 'smma-gutenberg-blocks' ) }
								allowedFormats={ [ 'core/bold' ] }
							/>
							<span
								className={ `smma-subscribe-now__button${
									plan.highlighted
										? ' smma-subscribe-now__button--highlighted'
										: ''
								}` }
							>
								{ plan.buttonLabel }
							</span>
							<p className="smma-subscribe-now__url-preview">
								→ { plan.redirectUrl }
							</p>
						</div>
					) ) }
				</div>
			</div>
		</>
	);
};

export default Edit;

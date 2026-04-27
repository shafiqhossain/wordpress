/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Save component for the Subscribe Now block.
 *
 * @param {Object} props            Block props.
 * @param {Object} props.attributes Block attributes.
 * @return {JSX.Element} Saved element.
 */
const Save = ( { attributes } ) => {
	const { blockTitle, plans } = attributes;
	const blockProps = useBlockProps.save( {
		className: 'smma-subscribe-now',
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName="h2"
				className="smma-subscribe-now__heading"
				value={ blockTitle }
			/>
			<div className="smma-subscribe-now__grid">
				{ plans.map( ( plan ) => (
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
								Recommended
							</div>
						) }
						<RichText.Content
							tagName="h3"
							className="smma-subscribe-now__plan-name"
							value={ plan.name }
						/>
						<RichText.Content
							tagName="p"
							className="smma-subscribe-now__plan-desc"
							value={ plan.description }
						/>
						<a
							className={ `smma-subscribe-now__button${
								plan.highlighted
									? ' smma-subscribe-now__button--highlighted'
									: ''
							}` }
							href={ plan.redirectUrl }
						>
							{ plan.buttonLabel }
						</a>
					</div>
				) ) }
			</div>
		</div>
	);
};

export default Save;

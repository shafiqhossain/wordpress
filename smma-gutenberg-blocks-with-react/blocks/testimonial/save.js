/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Save component for the Testimonials block.
 *
 * @param {Object} props            Block props.
 * @param {Object} props.attributes Block attributes.
 * @return {JSX.Element} Saved element.
 */
const Save = ( { attributes } ) => {
	const { testimonials } = attributes;
	const blockProps = useBlockProps.save( { className: 'smma-testimonial' } );

	return (
		<div { ...blockProps }>
			<h2 className="smma-testimonial__heading">
				What Our Clients Say
			</h2>
			<div className="smma-testimonial__grid">
				{ testimonials.map( ( t, i ) => (
					<div key={ t.id || i } className="smma-testimonial__card">
						<blockquote className="smma-testimonial__quote">
							"{ t.quote }"
						</blockquote>
						<div className="smma-testimonial__person">
							{ t.imageUrl ? (
								<img
									src={ t.imageUrl }
									alt={ t.imageAlt }
									className="smma-testimonial__avatar"
								/>
							) : (
								<div className="smma-testimonial__avatar-placeholder smma-testimonial__avatar-placeholder--small">
									{ ( t.personName || 'P' )[ 0 ].toUpperCase() }
								</div>
							) }
							<div className="smma-testimonial__meta">
								<strong className="smma-testimonial__name">
									{ t.personName }
								</strong>
								<span className="smma-testimonial__role">
									{ t.designation }
									{ t.designation && t.company ? ', ' : '' }
									{ t.company }
								</span>
							</div>
						</div>
					</div>
				) ) }
			</div>
		</div>
	);
};

export default Save;

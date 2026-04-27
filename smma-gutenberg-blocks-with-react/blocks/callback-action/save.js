/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Save component for the Callback Action block.
 *
 * @param {Object} props            Block props.
 * @param {Object} props.attributes Block attributes.
 * @return {JSX.Element} Saved element.
 */
const Save = ( { attributes } ) => {
	const { title, description, buttonLabel, buttonUrl } = attributes;
	const blockProps = useBlockProps.save( {
		className: 'smma-callback-action',
	} );

	return (
		<div { ...blockProps }>
			<div className="smma-callback-action__inner">
				<div className="smma-callback-action__content">
					<RichText.Content
						tagName="h2"
						className="smma-callback-action__title"
						value={ title }
					/>
					<RichText.Content
						tagName="p"
						className="smma-callback-action__description"
						value={ description }
					/>
				</div>
				<div className="smma-callback-action__action">
					<a
						className="smma-callback-action__button"
						href={ buttonUrl }
					>
						<RichText.Content value={ buttonLabel } />
					</a>
				</div>
			</div>
		</div>
	);
};

export default Save;

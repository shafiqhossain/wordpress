/**
 * WordPress dependencies
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Fetches dashboard stats from the REST API.
 *
 * @return {Promise<Object>} Resolved stats object.
 */
async function fetchStats() {
	const response = await window.fetch(
		window.wpApiSettings?.root
			? `${ window.wpApiSettings.root }smma/v1/dashboard-stats`
			: '/wp-json/smma/v1/dashboard-stats',
		{
			headers: {
				'X-WP-Nonce': window.wpApiSettings?.nonce || '',
			},
		}
	);
	if ( ! response.ok ) {
		throw new Error( 'Failed to fetch stats' );
	}
	return response.json();
}

/**
 * Edit component for the Dashboard Stats block.
 * Fetches live stats from the REST API so the editor preview is accurate.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor element.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const { blockTitle } = attributes;
	const blockProps = useBlockProps( { className: 'smma-dashboard-stats' } );

	const [ stats, setStats ]   = useState( null );
	const [ error, setError ]   = useState( null );

	useEffect( () => {
		fetchStats()
			.then( setStats )
			.catch( () =>
				setError(
					__(
						'Could not load stats. Make sure you are logged in as an editor.',
						'smma-gutenberg-blocks'
					)
				)
			);
	}, [] );

	const cards = [
		{
			label : __( 'Total Projects', 'smma-gutenberg-blocks' ),
			value : stats ? stats.total_projects : '—',
			color : 'blue',
			icon  : '📁',
		},
		{
			label : __( 'Total Subscribers', 'smma-gutenberg-blocks' ),
			value : stats ? stats.total_subscribers : '—',
			note  : __( 'Active & not expired', 'smma-gutenberg-blocks' ),
			color : 'green',
			icon  : '👥',
		},
		{
			label : __( 'Total Products', 'smma-gutenberg-blocks' ),
			value : stats
				? ( stats.woocommerce_active
					? stats.total_products
					: __( 'N/A', 'smma-gutenberg-blocks' ) )
				: '—',
			note  : stats && ! stats.woocommerce_active
				? __( 'WooCommerce not active', 'smma-gutenberg-blocks' )
				: '',
			color : 'purple',
			icon  : '🛒',
		},
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Block Settings', 'smma-gutenberg-blocks' ) }
					initialOpen={ true }
				>
					<p style={ { fontSize: '12px', color: '#555' } }>
						{ __(
							'This block is read-only on the front end. It displays live counts from the database. Only users with "edit_posts" capability can see it.',
							'smma-gutenberg-blocks'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="h2"
					className="smma-dashboard-stats__heading"
					value={ blockTitle }
					onChange={ ( val ) => setAttributes( { blockTitle: val } ) }
					placeholder={ __( 'Overview', 'smma-gutenberg-blocks' ) }
					allowedFormats={ [] }
				/>

				{ error && (
					<p className="smma-dashboard-stats__error">{ error }</p>
				) }

				<div className="smma-dashboard-stats__grid">
					{ cards.map( ( card, i ) => (
						<div
							key={ i }
							className={ `smma-dashboard-stats__card smma-dashboard-stats__card--${ card.color }` }
						>
							<span className="smma-dashboard-stats__emoji" aria-hidden="true">
								{ card.icon }
							</span>
							<span className="smma-dashboard-stats__value">
								{ stats ? card.value : (
									<span className="smma-dashboard-stats__skeleton" />
								) }
							</span>
							<span className="smma-dashboard-stats__label">{ card.label }</span>
							{ card.note ? (
								<span className="smma-dashboard-stats__note">{ card.note }</span>
							) : null }
						</div>
					) ) }
				</div>
			</div>
		</>
	);
};

export default Edit;

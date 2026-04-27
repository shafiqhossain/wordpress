/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import json from './block.json';
import './style.scss';
import './editor.scss';

const { name } = json;

/**
 * Edit component for the Recent Projects block.
 * Read-only in the editor: fetches projects via the REST API for preview.
 */
const Edit = () => {
	const blockProps = useBlockProps( { className: 'smma-recent-projects' } );

	const projects = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'smma_project', {
			per_page: 5,
			status: 'publish',
			orderby: 'date',
			order: 'desc',
			_embed: true,
		} );
	}, [] );

	if ( ! projects ) {
		return (
			<div { ...blockProps }>
				<div className="smma-recent-projects__loading">
					<Spinner />
					<p>{ __( 'Loading recent projects…', 'smma-gutenberg-blocks' ) }</p>
				</div>
			</div>
		);
	}

	if ( projects.length === 0 ) {
		return (
			<div { ...blockProps }>
				<p className="smma-recent-projects__empty">
					{ __(
						'No projects found. Add some projects first.',
						'smma-gutenberg-blocks'
					) }
				</p>
			</div>
		);
	}

	return (
		<div { ...blockProps }>
			<h2 className="smma-recent-projects__heading">
				{ __( 'Recent Projects', 'smma-gutenberg-blocks' ) }
			</h2>
			<div className="smma-recent-projects__grid">
				{ projects.map( ( project ) => {
					const startDate = project.meta?.smma_project_start_date || '';
					const endDate = project.meta?.smma_project_end_date || '';
					const shortDesc =
						project.meta?.smma_project_short_description ||
						project.excerpt?.rendered?.replace( /<[^>]+>/g, '' ) ||
						'';

					return (
						<article
							key={ project.id }
							className="smma-recent-projects__card"
						>
							<h3 className="smma-recent-projects__title">
								{ project.title.rendered }
							</h3>
							{ shortDesc && (
								<p className="smma-recent-projects__description">
									{ shortDesc }
								</p>
							) }
							<div className="smma-recent-projects__dates">
								{ startDate && (
									<span className="smma-recent-projects__date smma-recent-projects__date--start">
										<strong>
											{ __(
												'Start:',
												'smma-gutenberg-blocks'
											) }
										</strong>{ ' ' }
										{ startDate }
									</span>
								) }
								{ endDate && (
									<span className="smma-recent-projects__date smma-recent-projects__date--end">
										<strong>
											{ __(
												'End:',
												'smma-gutenberg-blocks'
											) }
										</strong>{ ' ' }
										{ endDate }
									</span>
								) }
							</div>
							<span className="smma-recent-projects__link smma-recent-projects__link--preview">
								{ __( 'View Project →', 'smma-gutenberg-blocks' ) }
							</span>
						</article>
					);
				} ) }
			</div>
			<p className="smma-recent-projects__editor-note">
				{ __(
					'ⓘ This block is read-only. It automatically displays the 5 most recent published projects.',
					'smma-gutenberg-blocks'
				) }
			</p>
		</div>
	);
};

registerBlockType( name, {
	edit: Edit,
	save: () => null, // Dynamic block — rendered via PHP callback.
} );

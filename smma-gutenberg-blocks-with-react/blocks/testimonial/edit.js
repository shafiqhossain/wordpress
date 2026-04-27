/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import {
	Button,
	TextareaControl,
	TextControl,
	PanelBody,
} from '@wordpress/components';
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Generates a simple unique ID for new testimonials.
 *
 * @return {string} Unique ID string.
 */
const generateId = () => 't_' + Date.now() + '_' + Math.random().toString( 36 ).slice( 2, 7 );

/**
 * Edit component for the Testimonials block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor element.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const { testimonials } = attributes;
	const blockProps = useBlockProps( { className: 'smma-testimonial' } );
	const [ activeIndex, setActiveIndex ] = useState( 0 );

	/**
	 * Updates a specific field of a testimonial by index.
	 *
	 * @param {number} index Index of the testimonial to update.
	 * @param {string} field Field name to update.
	 * @param {*}      value New value.
	 */
	const updateTestimonial = ( index, field, value ) => {
		const updated = testimonials.map( ( t, i ) =>
			i === index ? { ...t, [ field ]: value } : t
		);
		setAttributes( { testimonials: updated } );
	};

	/**
	 * Adds a new blank testimonial.
	 */
	const addTestimonial = () => {
		const newItem = {
			id: generateId(),
			quote: '',
			imageUrl: '',
			imageAlt: '',
			personName: '',
			designation: '',
			company: '',
		};
		const updated = [ ...testimonials, newItem ];
		setAttributes( { testimonials: updated } );
		setActiveIndex( updated.length - 1 );
	};

	/**
	 * Removes a testimonial by index.
	 *
	 * @param {number} index Index to remove.
	 */
	const removeTestimonial = ( index ) => {
		if ( testimonials.length === 1 ) {
			return;
		}
		const updated = testimonials.filter( ( _, i ) => i !== index );
		setAttributes( { testimonials: updated } );
		setActiveIndex( Math.min( activeIndex, updated.length - 1 ) );
	};

	const current = testimonials[ activeIndex ] || testimonials[ 0 ];
	const currentIndex = testimonials.indexOf( current );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Manage Testimonials', 'smma-gutenberg-blocks' ) }
					initialOpen={ true }
				>
					<p style={ { color: '#555', fontSize: '12px', lineHeight: '1.5' } }>
						{ __(
							'Use the block editor to add, edit, and remove testimonials. Select a testimonial card below to edit it.',
							'smma-gutenberg-blocks'
						) }
					</p>
					<p style={ { color: '#555', fontSize: '12px' } }>
						{ testimonials.length }{ ' ' }
						{ __( 'testimonial(s) added.', 'smma-gutenberg-blocks' ) }
					</p>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<h2 className="smma-testimonial__heading">
					{ __( 'What Our Clients Say', 'smma-gutenberg-blocks' ) }
				</h2>

				{/* Tab navigation */ }
				<div className="smma-testimonial__tabs">
					{ testimonials.map( ( t, i ) => (
						<button
							key={ t.id || i }
							className={ `smma-testimonial__tab${
								i === activeIndex
									? ' smma-testimonial__tab--active'
									: ''
							}` }
							onClick={ () => setActiveIndex( i ) }
						>
							{ t.personName ||
								`${ __( 'Testimonial', 'smma-gutenberg-blocks' ) } ${ i + 1 }` }
						</button>
					) ) }
					<button
						className="smma-testimonial__tab smma-testimonial__tab--add"
						onClick={ addTestimonial }
					>
						+ { __( 'Add', 'smma-gutenberg-blocks' ) }
					</button>
				</div>

				{/* Active testimonial editor */ }
				{ current && (
					<div className="smma-testimonial__editor-card">
						<div className="smma-testimonial__editor-image">
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) => {
										updateTestimonial( currentIndex, 'imageUrl', media.url );
										updateTestimonial( currentIndex, 'imageAlt', media.alt || '' );
									} }
									allowedTypes={ [ 'image' ] }
									value={ current.imageUrl }
									render={ ( { open } ) => (
										<div
											className="smma-testimonial__image-picker"
											onClick={ open }
										>
											{ current.imageUrl ? (
												<img
													src={ current.imageUrl }
													alt={ current.imageAlt }
													className="smma-testimonial__avatar"
												/>
											) : (
												<div className="smma-testimonial__avatar-placeholder">
													<span>{ __( 'Upload Photo', 'smma-gutenberg-blocks' ) }</span>
												</div>
											) }
										</div>
									) }
								/>
							</MediaUploadCheck>
							{ current.imageUrl && (
								<Button
									isDestructive
									isSmall
									onClick={ () => {
										updateTestimonial( currentIndex, 'imageUrl', '' );
										updateTestimonial( currentIndex, 'imageAlt', '' );
									} }
								>
									{ __( 'Remove Image', 'smma-gutenberg-blocks' ) }
								</Button>
							) }
						</div>

						<div className="smma-testimonial__editor-fields">
							<TextareaControl
								label={ __( 'Quote', 'smma-gutenberg-blocks' ) }
								value={ current.quote }
								onChange={ ( val ) =>
									updateTestimonial( currentIndex, 'quote', val )
								}
								placeholder={ __(
									'Enter testimonial quote…',
									'smma-gutenberg-blocks'
								) }
								rows={ 4 }
							/>
							<TextControl
								label={ __( 'Person Name', 'smma-gutenberg-blocks' ) }
								value={ current.personName }
								onChange={ ( val ) =>
									updateTestimonial( currentIndex, 'personName', val )
								}
								placeholder={ __( 'Jane Smith', 'smma-gutenberg-blocks' ) }
							/>
							<TextControl
								label={ __( 'Designation', 'smma-gutenberg-blocks' ) }
								value={ current.designation }
								onChange={ ( val ) =>
									updateTestimonial( currentIndex, 'designation', val )
								}
								placeholder={ __( 'Marketing Director', 'smma-gutenberg-blocks' ) }
							/>
							<TextControl
								label={ __( 'Company Name', 'smma-gutenberg-blocks' ) }
								value={ current.company }
								onChange={ ( val ) =>
									updateTestimonial( currentIndex, 'company', val )
								}
								placeholder={ __( 'Acme Corp', 'smma-gutenberg-blocks' ) }
							/>

							<Button
								isDestructive
								isSmall
								onClick={ () => removeTestimonial( currentIndex ) }
								disabled={ testimonials.length === 1 }
							>
								{ __( 'Remove This Testimonial', 'smma-gutenberg-blocks' ) }
							</Button>
						</div>
					</div>
				) }

				{/* Front-end preview of all cards */ }
				<div className="smma-testimonial__grid">
					{ testimonials.map( ( t, i ) => (
						<div
							key={ t.id || i }
							className={ `smma-testimonial__card${
								i === activeIndex
									? ' smma-testimonial__card--editing'
									: ''
							}` }
							onClick={ () => setActiveIndex( i ) }
						>
							<blockquote className="smma-testimonial__quote">
								"{ t.quote || __( 'Your testimonial quote will appear here.', 'smma-gutenberg-blocks' ) }"
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
										{ t.personName || __( 'Person Name', 'smma-gutenberg-blocks' ) }
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
		</>
	);
};

export default Edit;

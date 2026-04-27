/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import './style.scss';
import './editor.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	// Dynamic block — PHP render callback handles the front end.
	save: () => null,
} );

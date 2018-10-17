( function( editor, components, i18n, element ) {
	
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		InspectorControls = wp.editor.InspectorControls,
		TextControl = wp.components.TextControl,
		SelectControl = wp.components.SelectControl,
		RangeControl = wp.components.RangeControl,
		settings = orbit_settings ? orbit_settings : [];
		ServerSideRender = wp.components.ServerSideRender;

	registerBlockType( 'orbit-bundle/orbit-query', {
		
		title: 'Orbit Query',
		icon: 'media-text',
		category: 'widgets',
		
		attributes: settings['orbit_query_atts'],
		
		edit: function( props ) {
			
			// ensure the block attributes matches this plugin's name
			return [
				el( ServerSideRender, {
					block: "orbit-bundle/orbit-query",
					attributes:  props.attributes
				}),
				el( InspectorControls, { key: 'inspector' }, // Display the block options in the inspector panel.
					el( components.PanelBody, {
						title: i18n.__( 'Query Settings' ),
						initialOpen: true,
					},
						el( 'p', {}, i18n.__( 'Tweak the following query settings.' ) ),
						el( SelectControl, {
							label: i18n.__( 'Post Types' ),
							value: props.attributes.post_type,
							options: settings[ 'post_types' ],
							onChange: function( newPost_type ) {
								props.setAttributes( { post_type: newPost_type } );
							},
						} ),
						el( RangeControl, {
							label: i18n.__( 'Posts Per Page' ),
							value: props.attributes.posts_per_page,
							min: 1,
							max: 50,
							onChange: function( new_posts_per_page ) {
								props.setAttributes( { posts_per_page: new_posts_per_page } );
							},
						} ),
						el( SelectControl, {
							label: i18n.__( 'Template' ),
							value: props.attributes.style_id,
							options: settings[ 'styles' ],
							onChange: function( new_style_id ) {
								props.setAttributes( { style_id: new_style_id } );
							},
						} ),
						el( SelectControl, {
							label: i18n.__( 'Taxonomy' ),
							value: props.attributes.taxonomy,
							options: settings[ 'taxonomies' ],
							onChange: function( new_taxonomy ) {
								props.setAttributes( { taxonomy: new_taxonomy } );
								if( props.attributes.taxonomy && props.attributes.term ){
									props.setAttributes( { tax_query: props.attributes.taxonomy + ":" + props.attributes.term } );
								}
							},
						} ),
						el( TextControl, {
							type: 'string',
							label: i18n.__( 'Term Slug' ),
							value: props.attributes.term,
							onChange: function( new_term ) {
								props.setAttributes( { term: new_term } );
								if( props.attributes.taxonomy && props.attributes.term ){
									props.setAttributes( { tax_query: props.attributes.taxonomy + ":" + props.attributes.term } );
								}
							},
						} ),
					),
				),
			];
		},

		save: function() {
			// Rendering in PHP
			return null;
		},
	} );

} )(
	window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
);
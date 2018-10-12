<?php global $orbit_filter_obj;?>
<script id="tmpl-orbit-filter" type="text/html">
	<div id="orbit_backdrop"></div>
	<div id="orbit_content" class="form-wrap">
		<button type="button" class="media-modal-close">
			<span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span>
		</button>
		<h1 class="wp-heading-inline">Orbit Filter Form</h1>
		<hr>
		<div class='orbit_parent'>
			<form>
				<div class="form-field form-required">
					<label for="type">Select Type</label>
					
					<?php $types = $orbit_filter_obj->vars()['types'];?>
					
					<select name='type' id="type">
						<?php foreach( $types as $slug => $type ):?>
						<option <# if ( data.type == '<?php _e( $slug );?>' ) { #>selected='selected'<# } #>  value='<?php _e( $slug );?>'><?php _e( $type );?></option>
						<?php endforeach; ?>
					</select>
					<p>The primary type that needs to be filtered.</p>
				</div>
				
				<# if ( data.type == 'tax' ) { #> 
				<div class="form-field form-required">
					<label for="type">Select Taxonomy</label>
					<?php $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );?>
					<select name='typeval' id="typeval">
						<?php foreach( $taxonomies as $taxonomy ):?>
						<option <# if ( data.typeval == '<?php _e( $taxonomy->name );?>' ) { #>selected='selected'<# } #> value='<?php _e( $taxonomy->name );?>'><?php _e( $taxonomy->label." (".$taxonomy->name.")" );?></option>
						<?php endforeach;?>
						
					</select>
					<p>Taxonomy that needs to be filtered.</p>
				</div>
				<# } else{ #>
				<div class="form-field form-required">
					<label for="typeval">Slug</label>
					<input name="typeval" id="typeval" type="text" value="{{ data.typeval }}" size="40" aria-required="true">
					<p>Slug of the custom meta field.</p>
				</div>
				<# } #> 
				<div class="form-field form-required">
					<label for="form-type">Select Form</label>
					<?php $form_types = $orbit_filter_obj->vars()['forms'];?>
					<select name='form' id="form-type">
						<?php foreach( $form_types as $form_slug => $form_type ):?>
						<option <# if ( data.form == '<?php _e( $form_slug );?>' ) { #>selected='selected'<# } #> value='<?php _e( $form_slug );?>'><?php _e( $form_type );?></option>
						<?php endforeach;?>
					</select>
					<p>The form type that needs to be displayed.</p>
				</div>
				<div class="form-field form-required">
					<label for="label">Label</label>
					<input name="label" id="label" type="text" value="{{ data.label }}" size="40" aria-required="true">
					<p>Label for the filter.</p>
				</div>
				<# if ( data.form == 'typeahead' ) { #> 
				<div class="form-field form-required">
					<label for="placeholder">Placeholder</label>
					<input name="placeholder" id="placeholder" type="text" value="{{ data.placeholder }}" size="40" aria-required="true">
					<p>Placeholder of the input field.</p>
				</div>
				<# } #>
				<# if ( data.form != 'typeahead' && data.type == 'cf' ) { #> 
				<div class="form-field form-required">
					<label for="options">Options</label>
					<textarea rows="5" name="options" id="options" aria-required="true">{{ data.options }}</textarea>
					<p>Options of the input field. Type each option in a new line.</p>
				</div>	
				<# } #>
				<p class="submit"><input type="submit" name="submit" class="button button-primary" value="Create Shortcode"></p>
			</form>
			<div class='orbit_display'></div>
		</div>
	</div>
</script>
<script id="tmpl-orbit-query" type="text/html">
	<div id="orbit_backdrop"></div>
	<div id="orbit_content" class="form-wrap">
		<button type="button" class="media-modal-close">
			<span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span>
		</button>
		<h1 class="wp-heading-inline">Orbit Query Form</h1>
		<hr>
		<div class='orbit_parent'>
			<form>
				<div class="form-field form-required">
					<label for="post_type">Choose from Post Types</label>
					<?php $post_types = get_post_types( array( 'public' => true ), 'objects' );?>
					<select id="post_type" name="post_type">
					<?php foreach( $post_types as $type ):?>
					<option <# if( '<?php _e($type->name);?>' == data.post_type ){ #>selected='selected'<# } #> value="<?php _e( $type->name );?>"><?php _e( $type->label );?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="form-field form-required">
					<label for="posts_per_page">Posts Per Page</label>
					<input name="posts_per_page" id="posts_per_page" type="number" value="{{ data.posts_per_page }}" size="40" aria-required="true">
					<p>Total number of posts per page.</p>
				</div>
				<div class="form-field form-required">
					<label for="style_id">Choose Template</label>
					<?php global $orbit_templates; $templates = $orbit_templates->get_templates_list();?>
					<select name="style_id" id="style_id">
					<option value="0">Select</option>
					<?php foreach( $templates as $template ):?>
					<option <# if( '<?php _e($template->ID);?>' == data.style_id ){ #>selected='selected'<# } #> value="<?php _e( $template->ID );?>"><?php _e( $template->post_title );?></option>
					<?php endforeach;?>
					</select>
					<p>Select the template for the list of posts to be queried.</p>
				</div>
				<p class="submit"><input type="submit" name="submit" class="button button-primary" value="Create Shortcode"></p>
			</form>
			<div class='orbit_display'></div>
		</div>
	</div>
</script>
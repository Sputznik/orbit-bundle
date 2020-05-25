<?php

	// GET THE INITIAL REQUIRED DATA
	global $orbit_templates;
	$templates = $orbit_templates->get_templates_list();
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	// ADD TO DB
	if( isset( $_POST['submit'] ) && isset( $_POST['templates'] ) ){
		$orbit_templates->update_override_options( $_POST['templates'] );
	}

	// GET DATA FROM DB
	$data = $orbit_templates->get_override_options();

	//print_r( $data );

?>

<form method="POST">
	<p>Override post type templates</p>
	<?php /* TO BE ADDED LATER


	$val = isset( $data['author'] ) ? $data['author']:'0';?>
	<div class='box'>
		<h3>Author</h3>
		<p><label><?php _e( 'Author' );?></label></p>
		<p>
			<select name="<?php _e( 'templates[author]' );?>">
				<option value="0">Default</option>
				<?php foreach( $templates as $template ):?>
				<option <?php if( $val == $template->ID ){ _e("selected='selected'");}?> value="<?php _e( $template->ID );?>"><?php _e( $template->post_title );?></option>
				<?php endforeach;?>
			</select>
		</p>
	</div>

	*/ ?>

	<?php foreach( $post_types as $post_type ):?>
		<div class='box'>
			<h3><?php _e( $post_type->label );?></h3>
			<?php

				$template_types = array(
					'archives'	=> array(
						'label'	=> 'Archives Template',
					),
					'single'	=> array(
						'label'	=> 'Single Post Template'
					)
				);

			?>
			<?php foreach( $template_types as $template_slug => $template_type ):?>
			<?php
				// GET VALUE OF THE POST TYPE AND TEMPLATE
				$val = isset( $data[$post_type->name] ) && isset( $data[$post_type->name][$template_slug] ) ? $data[$post_type->name][$template_slug]:'0';
			?>
			<p><label><?php _e( $template_type['label'] );?></label></p>
			<p>
				<select name="<?php _e( 'templates['.$post_type->name.']['.$template_slug.']' );?>">
					<option value="0">Default</option>
					<?php foreach( $templates as $template ):?>
					<option <?php if( $val == $template->ID ){ _e("selected='selected'");}?> value="<?php _e( $template->ID );?>"><?php _e( $template->post_title );?></option>
					<?php endforeach;?>
				</select>
			</p>
			<?php endforeach;?>
		</div>
	<?php endforeach;?>
	<p class='submit'><input type="submit" name="submit" class="button button-primary" value="Save Changes"><p>
</form>
<style>
	.box{
		border: #ccc solid 1px;
		padding: 0 20px 20px 20px;
		margin: 10px;
		width: 170px;
		display: inline-block;
		background: #fff;
	}
	.box h3{
		background: #f7f7f7;
		margin: 0 -20px;
		padding: 20px;
		border-bottom: #ccc solid 1px;
	}
	@media( max-width: 768px ){
		.box{
			width: 70%;
		}
	}
</style>

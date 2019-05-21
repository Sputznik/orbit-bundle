<div class='orbit-search-form'>
	<div class="orbit-search-form-box">
		<div class="orbit-search-form-title">
			<span>Filter this data</span>
			<span class="arrow-down"></span>
		</div>
		<form method='GET'>
			<?php _e( do_shortcode( $form->post_content ) ); ?>
			<p><button type='submit'>Submit</button></p>
		</form>
	</div>
</div>

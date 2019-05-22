<p>Import predefined layouts as templates:</p>
<ol>
	<li>Three Column Grid to display posts</li>
	<li>Posts listed vertically with small thumbnails and post excerpts</li>
	<li>Posts listed vertically with huge thumbnails</li>
</ol>
<p class='help'>Note: if import has been done previously, then the items will be duplicated.</p>
<?php

	if( isset( $_POST['import'] ) && $_POST['import'] == '1' ){

		do_action('orbit_import');

		echo "<p><b>Import Successful</b></p>";

	}

?>
<?php if( ! isset( $_POST['import'] ) ):?>
<form method="POST">
	<input type="hidden" name="import" value="1" />
	<p class='submit'><input type="submit" name="submit" class="button button-primary" value="Import"><p>
</form>
<?php endif;?>

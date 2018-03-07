<ul>
<?php foreach ( $this->query->results as $user ):?>
	<li><?php echo $user->display_name;?></li>
<?php endforeach;?>
</ul>
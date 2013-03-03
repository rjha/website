<h4> Most Recent </h4>
<div class="feeds">
	<?php foreach($view->links as $link){ ?>
	<div class="item">
  		<a href="<?php echo $link['seo_title'] ?>"> <?php echo $link['title'] ?> </a>
	</div>
	<?php } ?>
	
</div>

<h4> Most Recent </h4>
<div class="menu">
	<?php foreach($view->links as $link){ ?>
	<div class="item">
  		<a href="<?php echo $link['seo_title'] ?>"> <?php echo $link['title'] ?> </a>
	</div>
	<?php } ?>
	<div class="p20">
		<a href="/sitemap" class="btn btn-large btn-inverse"> View all Posts &gt;</a>
	</div>
</div>

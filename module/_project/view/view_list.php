<body>
	<div id="header">
		<h2><?php echo $this->title; ?></h2>
	</div>
	<div id="list-main" class="bframe_adjustparent" data-param="margin:53">
		<div class="list-container">
			<?php echo $this->list->getHtml(); ?>
		</div>
	</div>
</body>

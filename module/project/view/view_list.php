<body>
	<div id="header">
		<h1><?php echo __('PROJECT'); ?></h1>
		<?php echo $this->header->gethtml(); ?>
	</div>

	<div id="list-main" class="bframe_adjustparent bframe_scroll" data-param="margin:162">
		<div class="list-container">
			<?php
				if($this->select_message) echo $this->select_message;
				echo $this->dg->getHtml($this->page_no);
			?>
		</div>
	</div>
</body>

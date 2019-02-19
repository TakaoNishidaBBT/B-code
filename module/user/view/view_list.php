<body>
	<div id="header">
		<h1><?php echo __('USER'); ?></h1>
		<?php echo $this->header->gethtml(); ?>
	</div>

	<div id="list-main" class="bframe_adjustparent bframe_scroll" data-param="margin:162">
		<div class="list-container">
			<form name="F1" id="F1" method="post" action="index.php" target="main">
				<?php
					if($this->select_message) echo $this->select_message;
					echo $this->dg->getHtml($this->page_no);
				?>
			</form>
		</div>
	</div>
</body>

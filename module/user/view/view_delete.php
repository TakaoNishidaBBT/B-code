<body>
	<form name="F1" method="post" action="index.php">
		<div id="header">
			<h1><?php echo __('USER'); ?></h1>

			<div class="form-header">
				<ul class="control">
					<li class="back button" onclick="bframe.submit('F1', 'user', 'list', 'back', '')">
						<span><?php echo __('Back To List'); ?></span>
					</li>
					<li class="button delete" onclick="bframe.confirmSubmit('<?php echo __('Are you sure you want to delete?'); ?>', 'F1', 'user', 'form', 'delete', '')">
						<span><?php echo __('Delete'); ?></span>
						<img src="images/common/del.png" alt="delete" />
					</li>
				</ul>
				<div class="message-container"><span id="message"></span></div>
			</div>
		</div>

		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:168">
			<div class="main-container">
				<div id="user-form">
					<?php echo $this->form->getHtml($this->display_mode); ?>
				</div>
				<div id="hidden-form">
					<?php echo $this->form->getHiddenHtml(); ?>
				</div>
			</div>
		</div>
	</form>
</body>

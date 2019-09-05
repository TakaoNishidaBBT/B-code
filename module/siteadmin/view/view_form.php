<body onload="bcode.setNavi()">
	<form name="F1" method="post" action="index.php">
		<div id="header">
			<h1><?php echo __('ADMIN PROFILE'); ?></h1>

			<div class="form-header">
				<ul class="control">
					<li class="dummy button"></li>
					<li id="submit-button" class="submit button" onclick="bframe.ajaxSubmit.submit('F1', 'siteadmin', 'form', 'register', 'update', true)">
						<span><?php echo __('Register'); ?></span>
						<img src="images/common/check.png" alt="submit" />
					</li>
				</ul>
				<div class="message-container"><span id="message"></span></div>
			</div>
		</div>

		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:168">
			<div class="main-container">
				<div id="admin-form">
					<?php echo $this->form->getHtml($this->display_mode); ?>
				</div>
				<div id="hidden-form">
					<?php echo $this->form->getHiddenHtml(); ?>
				</div>
			</div>
		</div>
	</form>
</body>

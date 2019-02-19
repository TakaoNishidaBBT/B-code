<body>
	<div id="header"><h1><?php echo __('USER'); ?></h1></div>

	<form name="F1" method="post" action="index.php">
		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:90">
			<div class="main-container">
				<ul class="control">
					<li class="back-button" onclick="bframe.submit('F1', 'user', 'list', 'back', '')">
						<img src="images/common/left_arrow_white.png" alt="left arow" />
						<span>Back To List</span>
					</li>
				</ul>
				<?php 
					if($this->action_message) {
						echo '<p class="error-message">' . $this->action_message . '</p>' . "\n";
					}
				?>
				<?php echo $this->form->getHtml($this->display_mode); ?>
				<ul class="submit">
					<li class="submit-button" onclick="bframe.registerUser('F1', 'user', 'form', 'register', '', true)">
						<span>Register</span>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this->form->getHiddenHtml(); ?>
	</form>
</body>

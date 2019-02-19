<body>
	<div id="header"><h1><?php echo __('PROJECT'); ?></h1></div>

	<form name="F1" method="post" action="index.php">
		<?php echo $this->form->getHiddenHtml(); ?>
		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:92">
			<div class="main-container">
				<ul class="control">
					<li class="back-button" onclick="bframe.submit('F1', 'project', 'list', 'back', '')">
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
					<li class="submit-button" onclick="bframe.registerProject('F1', 'project', 'form', 'register', '', true)">
						<span>Register</span>
					</li>
				</ul>
			</div>
		</div>
	</form>
</body>

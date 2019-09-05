<body>
	<form name="F1" method="post" action="index.php">
		<div id="header">
			<h1><?php echo __('USER'); ?></h1>

			<div class="form-header">
				<ul class="control">
					<li class="back button" onclick="bframe.submit('F1', 'user', 'list', 'back', '')">
						<span><?php echo __('Back To List'); ?></span>
					</li>
				</ul>
			</div>
		</div>

		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:168">
			<div class="main-container">
				<div class="delete-complete">
					<span class="user-name"><?php echo $this->user_name; ?></span> <?php echo __('was deleted!'); ?>
				</div>
			</div>
		</div>
	</form>
</body>

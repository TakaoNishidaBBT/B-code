<body>
	<form name="F1" method="post" action="index.php">
		<div id="header">
			<h1><?php echo __('PROJECT'); ?></h1>

			<div class="form-header">
				<ul class="control">
					<li class="back-button" onclick="bframe.submit('F1', 'project', 'list', 'back', '')">
						<img src="images/common/left_arrow_white.png" alt="left arow" />
						<span>Back To List</span>
					</li>
				</ul>
			</div>
		</div>

		<div id="main" class="bframe_adjustparent bframe_scroll" data-param="margin:168">
			<div class="main-container">
				<div class="delete-complete">
					<span class="project-name"><?php echo $this->project_name; ?></span> was deleted!
				</div>
			</div>
		</div>
	</form>
</body>
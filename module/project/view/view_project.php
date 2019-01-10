<body>
	<div id="select_category_container" class="bframe_adjustparent" data-param="margin:34">
		<?php echo $this->tree->getHtml(); ?>
	</div>
	<form name="F1" id="F1" method="post" action="index.php">
		<input type="hidden" id="node_id" name="node_id" value="" />
		<div class="control">
			<input type="button" class="cancel-button" value="Cancel" onclick="window.frameElement.deactivate();" />
			<input type="button" class="register-button" value="Open" onclick="bstudio.openProject()" />
		</div>
	</form>
</body>

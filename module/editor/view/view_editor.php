<body class="fadein" spellcheck="false">
	<div class="bframe_adjustparent" id="contents_editor_container">
		<div id="contents_editor" class="bframe_adjustparent">
			<div class="main_container bframe_adjustwindow" data-param="margin:0" >
				<form name="F1" id="F1" method="post" action="index.php">
					<?php echo $this->editor->getHtml(); ?>
				</form>
			</div>
		</div>
	</div>
	<script>
		document.body.addEventListener('mousedown', top.bstudio.mousedownBody);
	</script>
</body>

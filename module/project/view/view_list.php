<body onload="bstudio.setNavi()">
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
	<script>
		var container = document.querySelector('.list-container');
		var ul = document.querySelector('#entry_list');
		var lists = document.querySelectorAll('#entry_list li.project');
		var list = document.querySelector('#entry_list li.project');

		var style = bframe.getStyle(list);
		var list_width = parseInt(style.marginLeft) + list.clientWidth + parseInt(style.marginRight) + parseInt(style.borderLeftWidth) + parseInt(style.borderRightWidth);
		var w = list_width * lists.length;

		if(w < container.clientWidth) {
			ul.style.width = w + 'px';
		}
	</script>
</body>

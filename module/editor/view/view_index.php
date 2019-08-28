<body spellcheck="false" class="fadein">
	<div class="header"></div>
	<div id="bframe_tree_container" class="bframe_tree_container bframe_splitter_pane bframe_adjustwindow" data-param="margin:10">
		<?php echo $this->tree->getHtml(); ?>
	</div>
	<div id="splitter" class="splitter bframe_splitter" data-param="margin:10"></div>
	<div id="filemanager_pane" class="bframe_splitter_pane bframe_adjustwindow" data-param="margin:10">
		<div class="tabcontrol-container">
			<div class="scroller">
				<span id="tab_scroll_left"><img src="images/common/scroll_left.svg" alt="scroll left" /></span>
				<span id="tab_scroll_right"><img src="images/common/scroll_right.svg" alt="scroll right" /></span>
			</div>
			<ul id="tabcontrol" class="tabcontrol">
				<li class="selected"><a href="" class="tab"><span class="file_name">root</span></a><span class="close-button">×</span></li>
			</ul>
		</div>
		<div class="main_container bframe_adjustparent" data-param="margin:28">
			<div id="folder_container" class="bframe_adjustparent" data-param="margin:0">
				<ul class="resource_control">
					<li class="upload">
						<form id="form1" method="post" enctype="multipart/form-data">
							<div>
								<input id="upload_file" type="file" multiple="multiple" name="Filedata[]" class="bframe_uploader" style="display:none;" />
								<a href="#" title="click here or drop images to this pane"><span id="upload_button" class="upload-button"><img src="images/common/upload.png" alt="Upload" /><?php echo __('Upload'); ?></span></a>
							</div>
						</form>
					</li>
					<li id="reload_tree" class="view refresh"><a href="#" title="refresh"><img src="images/common/refresh.png" alt="refresh" /></a></li>
					<li id="display_detail" class="view detail"><a href="#" title="list view"><img src="images/common/view_detail.png" alt="view list" /></a></li>
					<li id="display_thumbnail" class="view thumbnail"><a href="#" title="thumbnail view"><img src="images/common/view_thumbnail.png" alt="view list" /></a></li>
				</ul>
				<div class="pane_container bframe_adjustparent" data-param="margin:44">
					<div id="bframe_pane" class="bframe_pane bframe_adjustparent bframe_scroll" data-param="margin:0,mode:filemanager"></div>
				</div>
			</div>
			<div id="editor_container" class="bframe_adjustparent" data-param="margin:14">
			</div>
		</div>
	</div>
	<div class="footer"></div>
</body>

<body>
		<div id="main" class="bframe_adjustparent" data-param="margin:40">
			<div id="left">
				<div class="left-inner bframe_scroll">
					<?php echo $this->dg_left->getHtml(); ?>
				</div>
			</div>
			<div class="control">
				<ul>
					<li><a id="add-button" href="#" ><img src="images/common/left_arrow_black.png" alt="left_arror" /></a></li>
					<li><a id="del-button" href="#" ><img src="images/common/right_arrow_black.png" alt="right_arror" /></a></li>
				</ul>
			</div>
			<div id="right">
				<ul class="search">
					<li class="search">
						<input class="keyword" maxlength="100" type="text" name="keyword" id="keyword" value="" />
						<input class="search-button" type="submit" name="search-button" id="search-button" value="Search"  />
					</li>
				</ul>
				<div class="right-inner bframe_scroll">
					<?php echo $this->dg_right->getHtml(); ?>
				</div>
			</div>
		</div>
		<form name="F1" method="post" action="index.php">
			<div class="control">
				<input type="button" class="cancel-button" value="Cancel" onclick="window.frameElement.deactivate();" />
				<input type="button" class="register-button" value="Set" onclick="bcode.setUser()" />
			</div>
		</form>
</body>

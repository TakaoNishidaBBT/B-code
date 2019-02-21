/*
 * B-frame : php web application framework
 * Copyright (c) BigBeat Inc. all rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	bframe.addEventListener(window, 'load' , bframeNaviInit);

	function bframeNaviInit(){
		var objects = document.getElementsByClassName('bframe_navi');
		for(var i=0; i < objects.length; i++) {
			bframe.gnavi = new bframe.navi_container(objects[i]);
		}
	}

	// -------------------------------------------------------------------------
	// class bframe.navi_container
	// 
	// -------------------------------------------------------------------------
	bframe.navi_container = function(target) {
		var navi = [];

	    var li = target.getElementsByTagName('li');
	    for(var i=0, j=0; i<li.length; i++) {
			navi[i] = new bframe.navi(li[i], this);
		}

		this.reset = function() {
			for(var i=0; i<navi.length; i++) {
				navi[i].reset();
			}
		}

		this.set = function(param) {
			for(var i=0; i<navi.length; i++) {
				if(navi[i].set(param)) {
					// scrollToElement
				}
			}
		}
	}

	// -------------------------------------------------------------------------
	// class bframe.navi
	// 
	// -------------------------------------------------------------------------
	bframe.navi = function(target, nc) {
		var target_id = bframe.getID(target);
		var navi_container = nc;
		var links = target.getElementsByTagName('a');
		var href = links[0].href;
		var query;
		var params = href.split('?');

		if(1 < params.length) {
			query = params[1]
		}

		this.reset = function() {
			target.classList.remove('current');
		}

		this.set = function(param) {
			if(query == param) {
				target.classList.add('current');
				return true;
			}
			else {
				target.classList.remove('current');
				return false;
			}
		}

		this.position = function() {
			return bframe.getElementPosition(target);
		}

		this.element = function() {
			return target;
		}
	}

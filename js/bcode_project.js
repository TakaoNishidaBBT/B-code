/*
 * B-code : Online Editor
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	window.addEventListener('load', function() {
		object = document.querySelector('ul.bcode_project');
		if(object) bcode_project = new bcode.project(object);
	});

	// -------------------------------------------------------------------------
	// class bcode.project
	// 
	// -------------------------------------------------------------------------
	bcode.project = function(target) {
		var projects = [];
		var drag_start, drag_target, drag_element;
		var relative_mouse_position;
		var drag_overlay = document.createElement('div');
		var projectOffset;
		var project_width, project_height;
		var target_index;
		var last_position;
		var scroll_out;
		var scroll_parent;
		var moving = 0;
		var storage = 'bcode-project';

		// set drag overlay
		drag_overlay.style.position = 'absolute'; 
		drag_overlay.style.top = '0'; 
		drag_overlay.style.right = '0'; 
		drag_overlay.style.bottom = '0'; 
		drag_overlay.style.left = '0'; 
		drag_overlay.style.opacity = '0.5'; 
		drag_overlay.style.cursor = 'move';
		drag_overlay.style.display = 'none';

		document.body.appendChild(drag_overlay);

		// set event handler
		bframe.addEventListenerAllFrames(top, 'mousemove', onMouseMove);
		drag_overlay.addEventListener('mousemove', onMouseMove);
		bframe.addEventListenerAllFrames(top, 'mouseup', onMouseUp);

		// initialize
		(function() {
			var item = restore();
			var p = target.querySelectorAll('li.project');

			for(let i=0; i < p.length; i++) {
				projects[i] = new project(p[i]);
				let order = i;
				if(item) {
					if(p[i].id in item) {
						order = item[p[i].id];
					}
					else {
						order = Object.keys(item).length + i;
					}
				}
				projects[i].setOrder(order);
			}

			// sort
			projects.sort(compare);

			project_width = projects[0].width();
			project_height = projects[0].height();

			scroll_parent = bframe.getScrollParent(target, true);
		})();

		function compare(a, b) {
			if(a.getOrder() > b.getOrder()) return 1;
		}

		function onMouseDown(event) {
			drag_target = bframe.getEventSrcElement(event).parentNode;
			style = bframe.getStyle(drag_target);
			drag_start = true;
			last_position = '';

			for(let i=0; i < projects.length; i++) {
				if(drag_target == projects[i].element()) {
					target_index = i;
					break;
				}
			}

			var scroll_offset = bframe.getScrollOffset(drag_target);
			var mouse_position = bframe.getMousePosition(event);
			var position = bframe.getElementPosition(drag_target);

			drag_overlay.style.display = 'block';
			drag_overlay.style.zIndex = '1001';
			drag_overlay.style.cursor = 'move';

			drag_clone = drag_target.cloneNode(true);
			drag_clone.classList.add('clone');
			drag_clone.style.position = 'absolute';
			drag_clone.style.zIndex = '1002';
			drag_clone.style.margin = 0;

			drag_clone.style.left = drag_target.offsetLeft - scroll_offset.left + 'px';
			drag_clone.style.top = drag_target.offsetTop - scroll_offset.top + 'px';

			document.body.appendChild(drag_clone);
			document.body.style.setProperty('user-select', 'none');
			document.body.style.setProperty('-ms-user-select', 'none');
			document.body.style.setProperty('-moz-user-select', 'none');
			document.body.style.setProperty('-webkit-user-select', 'none');

			relative_mouse_position = {
				x: mouse_position.x - parseInt(drag_clone.style.left),
				y: mouse_position.y - parseInt(drag_clone.style.top),
			}

			projectOffset = bframe.getElementPosition(scroll_parent);

			drag_target.style.visibility = 'hidden';
		}

		function onMouseMove(event) {
			if(!drag_start) return;

			var current_position = bframe.getMousePosition(event);
			if(!last_position) last_position = current_position;

			var deltaY = current_position.y - last_position.y > 0 ? 'down' : current_position.y - last_position.y < 0 ? 'up' : '';

			var left = current_position.x - relative_mouse_position.x;
			var top = current_position.y - relative_mouse_position.y;
			var right = left + project_width;
			var bottom = top + project_height;

			if(deltaY == 'down') {
				if(bottom > projectOffset.bottom) {
					if(!scroll_out)	{
						scroll_out = true;
						dragScroll('down');
					}
				}
				else {
					scroll_out = false;
				}
			}
			if(deltaY == 'up') {
				if(top < projectOffset.top) {
					if(!scroll_out)	{
						scroll_out = true;
						dragScroll('up');
					}
				}
				else {
					scroll_out = false;
				}
			}

			last_position = current_position;

			setClonePosition(left, top, right, bottom);

			checkProjectCrossover();
		}

		function onMouseUp(event) {
			if(!drag_start) return;

			drag_start = false;
			drag_overlay.style.display = 'none';
			drag_target.style.visibility = 'visible';
			document.body.removeChild(drag_clone);
		}

		function setClonePosition(left, top, right, bottom) {
			if(right > projectOffset.right) {
				left = projectOffset.right - project_width;
			}
			if(bottom > projectOffset.bottom) {
				top = projectOffset.bottom - project_height;
			}
			if(left < projectOffset.left) {
				left = projectOffset.left;
			}
			if(top < projectOffset.top) {
				top = projectOffset.top;
			}

			drag_clone.style.left = left + 'px';
			drag_clone.style.top = top + 'px';
		}

		function checkProjectCrossover() {
			if(moving) {
				setTimeout(checkProjectCrossover, 100);
				return;
			}

			var scroll_offset = bframe.getScrollOffset(drag_target);
			var clone_position = {
				x: parseInt(drag_clone.style.left) + scroll_offset.left,
				y: parseInt(drag_clone.style.top) + scroll_offset.top
			};

			for(let i=0; i < projects.length; i++) {
				if(drag_target == projects[i].element()) continue;

				var d = distance(clone_position, projects[i].position());
				if(d < project_width / 2 - 20) {
					move(target_index, i);
					setOrder();
					animateMove();
					target_index = i;
					save();
					break;
				}
			}
		}

		function distance(a, b) {
			var x = b.x - a.x;
			var y = b.y - a.y;
			return Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
		}

		function move(a, b) {
			projects.splice(b, 0, projects.splice(a, 1)[0]);
		}

		function swap(a, b) {
			projects.splice(b, 1, projects.splice(a, 1, projects[b])[0]);
		}

		function setOrder() {
			for(let i=0; i < projects.length; i++) {
				projects[i].lastPosition();
			}
			for(let i=0; i < projects.length; i++) {
				projects[i].setOrder(i);
			}
			for(let i=0; i < projects.length; i++) {
				projects[i].currentPosition();
			}
		}

		function animateMove() {
			target.style.height = target.clientHeight + 'px';

			for(let i=0; i < projects.length; i++) {
				if(drag_target == projects[i].element()) continue;

				projects[i].move(animateEndCallback);
			}
		}

		function animateEndCallback() {
			if(!moving) target.removeAttribute('style');
		}

		function dragScroll(direction) {
			let startScrollTop = scroll_parent.scrollTop;
			let start = performance.now();
			var momentum = 150;

			requestAnimationFrame(function animate(time) {
				if(!scroll_out) return;

				let timeFraction = time - start;
				let left;

				if(direction == 'down') {
					scroll_parent.scrollTop = startScrollTop + Math.round(timeFraction * momentum / 200);
				}
				else {
					scroll_parent.scrollTop = startScrollTop - Math.round(timeFraction * momentum / 200);
				}
				if((direction == 'down' && scroll_parent.scrollTop < scroll_parent.scrollHeight - scroll_parent.clientHeight) ||
				 (direction == 'up' && scroll_parent.scrollTop > 0)) {
					requestAnimationFrame(animate);
				}
			});
		}

		function save() {
			var item = {};

			for(let i=0; i < projects.length; i++) {
				item[projects[i].id()] = projects[i].getOrder();
			}

			var item_json = JSON.stringify(item);
			localStorage.setItem(storage, item_json);
		}
		this.save = save;

		function restore() {
			var item_json = localStorage.getItem(storage);
			if(item_json) {
				return JSON.parse(item_json);
			}
			else {
				return {};
			}
		}

		// -------------------------------------------------------------------------
		// class project
		// 
		// -------------------------------------------------------------------------
		function project(element) {
			var id = element.id;
			var name = element.querySelector('.name');
			var current_position;
			var last_position;
			var order;

			// set event handler
			name.addEventListener('mousedown', onMouseDown);

			this.id = function() {
				return id;
			}

			this.element = function() {
				return element;
			}

			this.width = function() {
				return element.clientWidth;
			}

			this.height = function() {
				return element.clientHeight;
			}

			this.position = function() {
				return {x: element.offsetLeft, y: element.offsetTop};
			}

			this.currentPosition = function() {
				current_position = {x: element.offsetLeft, y: element.offsetTop};
			}

			this.lastPosition = function() {
				last_position = {x: element.offsetLeft, y: element.offsetTop};
			}

			this.setOrder = function(i) {
				element.style.order = order = i;
			}

			this.getOrder = function(i) {
				return order;
			}

			this.move = function(endCallback=null) {
				let from = last_position;
				let to = current_position;
				let move_x = to.x - from.x;
				let move_y = to.y - from.y;

				element.style.left = from.x + 'px';
				element.style.top = from.y  - scroll_parent.scrollTop + 'px';
				element.style.margin = '0';
				element.style.position = 'absolute';

				moving++;

				animate(
					function(t) {
						return (--t)*t*t+1;
					},
					function(progress) {
						element.style.left = from.x + Math.round(move_x * progress) + 'px';
						element.style.top = from.y + Math.round(move_y * progress) - scroll_parent.scrollTop + 'px';
					},
					400,
					function() {
						element.removeAttribute('style');
						element.style.order = order;
						moving--;
						if(endCallback) endCallback();
					}
				);

			}

			function animate(timing, callback, duration, endCallBack=null) {
				let start = performance.now();

				requestAnimationFrame(function animate(time) {
					// timeFraction goes from 0 to 1
					let timeFraction = (time - start) / duration;
					if(timeFraction > 1) timeFraction = 1;

					// calculate the current animation state
					let progress = timing(timeFraction);

					callback(progress); // callback function

					if(timeFraction < 1) {
						requestAnimationFrame(animate);
					}
					else if(endCallBack) {
						endCallBack();
					}
				});
			}
		}
	}
/**
 * Main Singleton handling an entire timeline
 */
var Timeline = (function () {

	var snappingTime = 120000; // in milliseconds
	var line = null;
	var items = null;
	var jQueryElement = null;
	var date = null;
	function initialize(container, inputDate, options) {
		// Create a DataSet (allows two way data-binding)
		items = new vis.DataSet([]);

		// global date information
		date = inputDate;

		// options generation
		var visOptions = generateOptions(date, options ? options : {});

		// Create a Timeline
		line = new vis.Timeline(container, items, visOptions);

		if(options.autoRefresh) {
			line.on("currentTimeTick", currentTimeRefresher);
		}

		line.on("select", onSelect);
		if(options.navigationButtons) {
			document.getElementById('zoomIn').onclick    = function () { line.zoomIn( 0.2); };
			document.getElementById('zoomOut').onclick   = function () { line.zoomOut( 0.2); };
			document.getElementById('moveLeft').onclick  = function () { move( 0.2); };
			document.getElementById('moveRight').onclick = function () { move(-0.2); };
		}

		jQueryElement = $(container);

		return line;
	}

	function add(video, pxPos) {
		if(pxPos) {
			var lineWindowStart = line.getWindow().start.getTime();
			var lineWindowEnd = line.getWindow().end.getTime();
			var dpi = (lineWindowEnd - lineWindowStart) / jQueryElement.width();
			video.start = new Date(lineWindowStart + Math.floor(pxPos * dpi));
			video.startDate = {
				date: moment(video.start).format("DD/MM/YYYY"),
				time: moment(video.start).format("HH:mm:ss")
			};
			video.className = video.sourceType;
		}
		switch (video.sourceType) {
			case "LIVE":
				video.end = new Date(video.start.getTime() + durationToMS(video.duration));
				break;
			case "PRERECORDED":
				video.end = new Date(video.start.getTime() + durationToMS(video.duration));
				break;
		}
		video.endDate = {
			date: moment(video.end).format("DD/MM/YYYY"),
			time: moment(video.end).format("HH:mm:ss")
		};
		if(!checkOverlap(video, true)) {
			items.add(video);
		} else {
			console.log("Already present. Don't add");
		}
	}

	function addItem(item) {
		if(!checkOverlap(item, false)) {
			item.className = item.sourceType;
			items.add(item);
		} else {
			console.log("Already present. Don't add");
		}
	}

	/**
	 * Major item event listener.
	 * Fired each time an object is being moved or time-updated.
	 * Handles these cases:
	 * 	1. Item must not be overlapped by any other item. (function *checkValidity* does most of this)
	 * 	2. Item must be a *LIVE* if you want to edit duration
	 * @param item
	 * @param callback
	 */
	function onMoving(item, callback) {
		item.moving = true;		// mark the item as moving

		var overlap = false;
		if(line.getSelection().length > 1) {
			overlap = checkMultipleOverlap(item);
		} else {
			overlap = checkOverlap(item, true);
			snapItem(item);
		}

		if(!overlap) {
			callback(item);
		}
	}

	function checkOverlap(item, forceSameDay) {
		var itemStart = typeof item.start === "number" ? item.start : item.start.getTime();
		var itemEnd = typeof item.end === "number" ? item.end : item.end.getTime();

		if(durationToMS(item.duration) !== itemEnd - itemStart) {
			console.log("wrong length");
			return true;
		}

		// if(isMultipleOverlap()) {
		// 	return true;
		// }

		var visibles = line.getVisibleItems();


		// cycling through all visible items
		for (var i = 0; i < visibles.length; i++) {
			// the visible item i'm checking
			var visible = line.itemSet.items[visibles[i]].data;
			var visibleStart = typeof visible.start === "number" ? visible.start : visible.start.getTime();
			var visibleEnd = typeof visible.end === "number" ? visible.end : visible.end.getTime();

			// must be not the same id and overlapping
			if(item.id !== visible.id) {
				if(check(itemStart, itemEnd, visibleStart, visibleEnd)) {
					// if(!visible.moving && !item.snappedItem) {
						console.log("overlapping");
						return true;
					// }
				}
			}
		}
		// return forceSameDay ? moment(date).isSame(moment(itemStart), 'day') : false; 			// startDate has to be the day I'm Editing
		console.log("ok");
		return false;
	}

	function checkMultipleOverlap(item) {
		var visibles = line.getVisibleItems();

		if(item.id === groupItems[0]) {
			// checking first element of the group
			var difference = groupStart - item.start.getTime();
			groupStart = item.start.getTime();
			groupEnd -= difference;
		}
		if(item.id === groupItems[groupItems.length-1]) {
			// checking last element of the group
			var difference = groupEnd - item.end.getTime();
			groupEnd = item.end.getTime();
			groupStart -= difference;
		}

		// cycling through all visible items
		for (var i = 0; i < visibles.length; i++) {
			// the visible item i'm checking
			var visible = line.itemSet.items[visibles[i]].data;
			var visibleStart = typeof visible.start === "number" ? visible.start : visible.start.getTime();
			var visibleEnd = typeof visible.end === "number" ? visible.end : visible.end.getTime();

			var isMultipleSelected = groupItems.indexOf(visible.id) !== -1;
			// must be not the same id and overlapping
			if(!isMultipleSelected) {
				if(check(groupStart, groupEnd, visibleStart, visibleEnd)) {
					// if(!visible.moving && !item.snappedItem) {
					console.log("overlapping");
					return true;
					// }
				} else {
					if(groupStart > visibleEnd && groupStart < visibleEnd + snappingTime) {
						console.log("snap to previous");
						// groupSnap(groupStart - visibleEnd, visible);
					}
					else if (groupEnd < visibleStart && groupEnd > visibleStart - snappingTime) {
						console.log("snap to next");
					// 	// groupSnap(groupEnd - visibleStart, visible);
					}
				}
			}
		}
		// return forceSameDay ? moment(date).isSame(moment(itemStart), 'day') : false; 			// startDate has to be the day I'm Editing
		console.log("ok");
		return false;


		function groupSnap(difference, snappingElement) {
			for(var i = 0; i < groupItems.length; i++) {
				var elem = line.itemSet.items[groupItems[i]].data;
				// var elemStart = typeof elem.start === "number" ? elem.start : elem.start.getTime();
				// var elemEnd = typeof elem.end === "number" ? elem.end : elem.end.getTime();

				elem.start -= difference;
				elem.end -= difference;
				elem.snappedItem = snappingElement.id;
			}
		}
	}

	function snapItem(item) {
		var visibles = line.getVisibleItems();

		var itemStart = typeof item.start === "number" ? item.start : item.start.getTime();
		var itemEnd = typeof item.end === "number" ? item.end : item.end.getTime();

		// cycling through all visible items
		for (var i = 0; i < visibles.length; i++) {
			// the visible item i'm checking
			var visible = line.itemSet.items[visibles[i]].data;
			var visibleStart = typeof visible.start === "number" ? visible.start : visible.start.getTime();
			var visibleEnd = typeof visible.end === "number" ? visible.end : visible.end.getTime();

			// snap
			if (Math.abs(itemStart - visibleEnd) < snappingTime && (!item.snappedItem || item.snappedItem === visible.id)) {
				// snap item to end of previous one.
				item.snappedItem = visible.id;
				item.start = visible.end;
				item.end -= (itemStart - visibleEnd);
			} else if (Math.abs(itemEnd - visibleStart) < snappingTime && (!item.snappedItem || item.snappedItem === visible.id)) {
				// snap item to start of next one.
				item.snappedItem = visible.id;
				item.start -= (itemEnd - visibleStart);
				item.end = visible.start;
			} else {
				item.snappedItem = null;
			}

		}
	}

	var groupItems = [];
	var groupStart;
	var groupEnd;
	function onSelect(arguments) {

		var selection = arguments.items;
		var lastItem = line.getEventProperties(arguments.event).item;

		switch (selection.length) {
			case 0:
				// nothing selected. All to initial state
				groupItems = [];
				groupStart = null;
				groupEnd = null;
				break;
			case 1:
				// only one object. All to it
				groupItems = selection;
				var elem = line.itemSet.items[lastItem].data;
				groupStart = typeof elem.start === "number" ? elem.start : elem.start.getTime();
				groupEnd = typeof elem.end === "number" ? elem.end : elem.end.getTime();
				break;
			default:
				// selecting more than one elements
				var elem = line.itemSet.items[lastItem].data;
				var elemStart = typeof elem.start === "number" ? elem.start : elem.start.getTime();
				var elemEnd = typeof elem.end === "number" ? elem.end : elem.end.getTime();

				var index = groupItems.indexOf(lastItem);
				if(index === -1) {
					// adding an element
					if(groupEnd === elemStart) {
						// adding after all other elements
						groupEnd = elemEnd;
						groupItems.push(lastItem);
					} else if(groupStart === elemEnd) {
						// adding as first element
						groupStart = elemStart;
						groupItems.unshift(lastItem);
					} else {
						// tried to add an element non consequential. Forbidden: force previous selection
						line.setSelection(groupItems);
					}
				} else {
					// removing an element
					if(elem.id === groupItems[0]) {
						// removing the first element
						groupItems.splice(index, 1);
						groupEnd = elemStart;
					} else if (elem.id === groupItems[groupItems.length-1]) {
						// removing last element
						groupItems.splice(index, 1);
						groupStart = elemEnd;
					} else {
						// removing a middle element. Forbidden: clear all elements
						line.setSelection([]);
					}


					if(groupEnd === elemEnd) {
						groupEnd = elemStart;
					} else if(groupStart === elemStart) {
						groupStart = elemEnd;
					} else {
						console.log("ERROR THIS IS NOT CATCHED: Resetting");
						line.setSelection([]);
					}
					groupItems.splice(index, 1);
				}
				break;
		}

		console.log(groupItems);

		// if(selection.length === 0) {
		// 	groupStart = null;
		// 	groupEnd = null;
		// } else {
		// 	var elem = line.itemSet.items[lastItem].data;
		// 	var elemStart = typeof elem.start === "number" ? elem.start : elem.start.getTime();
		// 	var elemEnd = typeof elem.end === "number" ? elem.end : elem.end.getTime();
		// 	if(selection.length === 1) {
		// 		groupStart = elemStart;
		// 		groupEnd = elemEnd;
		// 	} else {
		// 		if(elemStart === groupEnd) {
		// 			groupEnd = elemEnd;
		// 		} else if(groupStart === elemEnd) {
		// 			groupStart = elemStart;
		// 		}
		// 	}
		// }
	}
	
	function onMove(item, callback) {
		item.moving = false;
		callback(item)
	}

	function move (percentage) {
		var range = line.getWindow();
		var interval = range.end - range.start;

		line.setWindow({
			start: range.start.valueOf() - interval * percentage,
			end:   range.end.valueOf()   - interval * percentage
		});
	}

	// SUPPORT FUNCTIONS
	function check(itemStart, itemEnd, elemStart, elemEnd) {
		return (itemStart > elemStart && itemStart < elemEnd) ||		// itemStart in elem
			(itemEnd > elemStart && itemEnd < elemEnd) ||			// itemEnd in elem
			(elemStart >= itemStart && elemEnd <= itemEnd) ||		// elem in item
			(itemStart >= elemStart && itemEnd <= elemEnd);		// item in elem
	}

	function generateOptions(date, options) {
		var momentDate = moment(date);
		return {
			selectable: options.selectable ? options.selectable : false,
			multiselect: options.selectable ? options.selectable : false,
			editable: {
				updateTime: options.editable ? options.editable : false,
				remove: options.editable ? options.editable : false
			},
			onMoving: onMoving,
			onMove: onMove,
			start: options.start ? options.start : momentDate.toDate(),
			end: options.end ? options.end : momentDate.date(momentDate.date()+1).toDate(),
			max: options.max ? options.max : momentDate.date(momentDate.date()+1).toDate(),
			min: options.min ? options.min : momentDate.date(momentDate.date()-1).toDate(),
			height: options.height ? options.height : '180px',
			zoomMin: options.zoomMin ? options.zoomMin : 60000,		// 10 seconds
			zoomMax: options.zoomMax ? options.zoomMax : 604800000,	// 1 week
			stack: false,
			snap: null,
			template: function (item) {
				var html = '';
				html += '<h3>' + item.content + '</h3>';
				if (!options.hideImage) {
					html += '<div class="image-rectangle-box cast-image" style="background-image: url(' + item.thumbnailUrl + ');"></div>';
				}
				if (!options.hideType) {
					html += '<p>&nbsp;</p>';
				}
				return html;
			}
		};
	}

	function durationToMS(textDuration) {
		if(typeof textDuration === 'number') {
			return textDuration;
		}
		var split = textDuration.split(":");

		var seconds = 0;
		var minutes = 0;
		var hours = 0;

		if(split.length === 2) {
			minutes = split[0];
			seconds = split[1];
		}

		if(split.length === 3) {
			hours = split[0];
			minutes = split[1];
			seconds = split[2];
		}

		return (((hours * 60) - - minutes) * 60 - - seconds) * 1000;
	}

	return {
		init: initialize,
		add: add,
		addItem: addItem
	}
})();
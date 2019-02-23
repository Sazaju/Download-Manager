function findLeft(item) {
	ttop = 0;
	while(item) {
		ttop += item.offsetLeft;
		item = item.offsetParent;
	}
	return ttop;
}

function findTop(item) {
	ttop = 0;
	while(item) {
		ttop += item.offsetTop;
		item = item.offsetParent;
	}
	return ttop;
}

function autoResize(item) {
	originalWidth = item.width;
	originalHeight = item.height;
	
	windowWidth = window.innerWidth;
	windowHeight = window.innerHeight;
	originalX = findLeft(item);
	originalY = findTop(item);
	scrollBarSize = 20;
	availableWidth = windowWidth - originalX - scrollBarSize;
	availableHeight = windowHeight - originalY;
	
	ratioWidth = availableWidth/originalWidth;
	ratioHeight = availableHeight/originalHeight;
	
	if (ratioWidth == NaN || ratioHeight == NaN) {
		alert("Cannot get size, don't auto-resize.");
	} else if (ratioWidth > ratioHeight) {
		item.style.width = 'auto';
		item.style.height = availableHeight+'px';
	} else {
		item.style.width = availableWidth+'px';
		item.style.height = 'auto';
	}
}

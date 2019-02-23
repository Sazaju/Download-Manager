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

function autoResize(image) {
	originalWidth = image.width;
	originalHeight = image.height;
	originalX = findLeft(image);
	originalY = findTop(image);
	originalRatio = originalWidth/originalHeight;
	
	windowWidth = window.innerWidth;
	windowHeight = window.innerHeight;
	
	availableWidth = windowWidth - originalX - 20;// 20 = potential scroll bar size
	availableHeight = windowHeight - originalY;
	availableRatio = availableWidth/availableHeight;
	
	if (availableRatio > originalRatio) {
		image.style.width = 'auto';
		image.style.height = availableHeight+'px';
	} else {
		image.style.width = availableWidth+'px';
		image.style.height = 'auto';
	}
}

function findLeft(iobj) {
	ttop = 0;
	while(iobj) {
		ttop += iobj.offsetLeft;
		iobj = iobj.offsetParent;
	}
	return ttop;
}

function findTop(iobj) {
	ttop = 0;
	while(iobj) {
		ttop += iobj.offsetTop;
		iobj = iobj.offsetParent;
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

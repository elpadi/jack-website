function SynchScroll() {
}

Object.defineProperty(SynchScroll.prototype, 'init', {
	value: function init() {
		['left','right'].forEach(function(side) { this[side] = $('.synch-scroll--' + side); }.bind(this));
	}
});

Object.defineProperty(SynchScroll.prototype, 'initPosMatch', {
	value: function initPosMatch() {
		this.OFFSET_TOP = this.left.offset().top;
		if (location.hash.length > 1) document.getElementById(location.hash.substr(1)).scrollIntoView();
		else this.scroll();
	}
});

Object.defineProperty(SynchScroll.prototype, 'scroll', {
	value: function scroll(scrollY) {
		if (!('scrollItems' in this) || !('OFFSET_TOP' in this)) return;
		var index = 0, h, rect, y, l = this.fixedItems.length;
		console.group('scroll, OFFSET_TOP: ' + this.OFFSET_TOP);
		do {
			rect = this.scrollItems[index].getBoundingClientRect();
			valid = true;
			if (rect.bottom < 0) {
				valid = false;
				console.log(index, 'above the sky');
			}
			if (rect.top > window.innerHeight) {
				valid = false;
				console.log(index, 'below the sea');
			}
			if (valid) {
				console.group('scroll ' + index);
				h = this.fixedItems[index].offsetHeight;
				if (rect.top > this.OFFSET_TOP) {
					console.log('down below, keep titles in same line');
					y = rect.top;
				}
				else if (rect.bottom < this.OFFSET_TOP + h) {
					console.log('moving up, keep bottoms in same line');
					y = rect.bottom - h;
				}
				else {
					console.log('in the middle, stay neutral');
					y = this.OFFSET_TOP;
				}
				$(this.fixedItems[index]).css('transform', 'translateY(' + Math.round(y) + 'px)');
				console.groupEnd();
			}
			else $(this.fixedItems[index]).css('transform', '');
			index++;
		}
		while (index < l);
		console.groupEnd();
	}
});

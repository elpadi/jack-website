function SynchScroll() {
}

Object.defineProperty(SynchScroll.prototype, 'init', {
	value: function init() {
		['left','right'].forEach(function(side) { this[side] = $('.synch-scroll--' + side); }.bind(this));
		this.OFFSET_TOP = this.left.parent().get(0).getBoundingClientRect().top;
	}
});

Object.defineProperty(SynchScroll.prototype, 'scroll', {
	value: function scroll(scrollY) {
		if (!('scrollItems' in this)) return;
		var index = 0, h, rect, y, l = this.fixedItems.length;
		do {
			console.group('scroll ' + index);
			rect = this.scrollItems[index].getBoundingClientRect();
			valid = true;
			if (rect.bottom < 0) {
				valid = false;
				console.log('above the sky');
			}
			if (rect.top > window.innerHeight) {
				valid = false;
				console.log('below the sea');
			}
			if (valid) {
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
			}
			else $(this.fixedItems[index]).css('transform', '');
			index++;
			console.groupEnd();
		}
		while (index < l);
	}
});

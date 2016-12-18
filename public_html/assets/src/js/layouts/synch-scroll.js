function SynchScroll() {
	this.FIXED_TOP = 190;
}

Object.defineProperty(SynchScroll.prototype, 'init', {
	value: function init() {
		this.items = $('.synch-scroll__item');
		/*
		this.fixed = document.getElementById('fixed-issue-section');
		this.sections = Array.from(document.getElementsByClassName('issue-section')).filter(function(node) { return node.id == ''; });
		*/
	}
});

Object.defineProperty(SynchScroll.prototype, 'resize', {
	value: function resize() {
		this.sections.forEach(function(node) {
			node.children[1].style.height = Math.max.apply(window, Array.from(node.children).map(function(childNode) { return childNode.offsetHeight; })) + 'px';
		});
	}
});

Object.defineProperty(SynchScroll.prototype, 'hide', {
	value: function hide(index) {
		this.fixed.children[index].classList.remove('visible');
		this.sections[index].children[0].classList.remove('hidden');
	}
});

Object.defineProperty(SynchScroll.prototype, 'show', {
	value: function show(index) {
		this.fixed.children[index].classList.add('visible');
		this.sections[index].children[0].classList.add('hidden');
		this.fixed.style.height = this.sections[index].children[0].offsetHeight + 'px';
	}
});

Object.defineProperty(SynchScroll.prototype, 'ahead', {
	value: function ahead(index) {
		this.hide(index);
		this.sections[index].classList.remove('behind');
	}
});

Object.defineProperty(SynchScroll.prototype, 'behind', {
	value: function behind(index) {
		this.hide(index);
		this.sections[index].classList.add('behind');
	}
});

Object.defineProperty(SynchScroll.prototype, 'scroll', {
	value: function scroll() {
		var current = NaN;
		this.sections.forEach(function(node, index) {
			if (index > current) return this.ahead(index);
			var rect = node.getBoundingClientRect();
			var fixedBottom = this.FIXED_TOP + this.sections[index].children[0].offsetHeight;
			if (rect.top <= this.FIXED_TOP && rect.bottom > fixedBottom) {
				current = index;
				this.show(index);
			}
			if (rect.top > this.FIXED_TOP) return this.ahead(index);
			if (rect.bottom <= fixedBottom) return this.behind(index);
		}.bind(this));
	}
});

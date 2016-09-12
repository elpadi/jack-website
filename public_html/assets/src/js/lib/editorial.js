function Editorial() {
	this.FIXED_TOP = 190;
}

Object.defineProperty(Editorial.prototype, 'init', {
	value: function init() {
		this.fixed = document.getElementById('fixed-issue-section');
		this.sections = Array.from(document.getElementsByClassName('issue-section')).filter(function(node) { return node.id == ''; });
	}
});

Object.defineProperty(Editorial.prototype, 'resize', {
	value: function resize() {
		this.sections.forEach(function(node) {
			node.children[1].style.height = Math.max.apply(window, Array.from(node.children).map(function(childNode) { return childNode.offsetHeight; })) + 'px';
		});
	}
});

Object.defineProperty(Editorial.prototype, 'hide', {
	value: function hide(index) {
		this.fixed.children[index].classList.remove('visible');
		this.sections[index].children[0].classList.remove('hidden');
	}
});

Object.defineProperty(Editorial.prototype, 'show', {
	value: function show(index) {
		this.fixed.children[index].classList.add('visible');
		this.sections[index].children[0].classList.add('hidden');
		this.fixed.style.height = this.sections[index].children[0].offsetHeight + 'px';
	}
});

Object.defineProperty(Editorial.prototype, 'ahead', {
	value: function ahead(index) {
		this.hide(index);
		this.sections[index].classList.remove('behind');
	}
});

Object.defineProperty(Editorial.prototype, 'behind', {
	value: function behind(index) {
		this.hide(index);
		this.sections[index].classList.add('behind');
	}
});

Object.defineProperty(Editorial.prototype, 'scroll', {
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

App.instance.addChild('editorial', new Editorial());

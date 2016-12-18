function GridSlots(size) {
	this.size = size;
	this.currentIndex = 0;
	this.slots = [];
	this.counts = [];
}

Object.defineProperty(GridSlots.prototype, 'nextIndex', {
	value: function nextIndex(size) {
		var index = this.counts.findIndex(function(count) {
			return count + size <= this.size;
		}.bind(this));
		if (index !== -1) return index;
		this.slots.push([]);
		this.counts.push(0);
		return this.slots.length - 1;
	}
});

Object.defineProperty(GridSlots.prototype, 'add', {
	value: function add(size, item) {
		var index = this.nextIndex(size);
		this.counts[index] += size;
		this.slots[index].push(item);
	}
});

function Editorial() {
}

Object.defineProperty(Editorial.prototype, 'init', {
	value: function init() {
		this.grid = new GridSlots(4);
		this.images = jQuery('.issue-images').find('img');
	}
});

Object.defineProperty(Editorial.prototype, 'load', {
	value: function load() {
		this.gridSort();
		setTimeout(this.gridRowHeight.bind(this), 200);
	}
});

Object.defineProperty(Editorial.prototype, 'gridRowHeight', {
	value: function gridRowHeight() {
		this.rows.forEach(function(row) {
			row.style.height = (Math.min.apply(window, _.pluck(row.children, 'offsetHeight')) - 1) + 'px';
		});
	}
});

Object.defineProperty(Editorial.prototype, 'gridSort', {
	value: function gridSort() {
		var images = this.images.get();
		var container = images[0].parentNode;
		shuffle(images);
		this.rows = [];
		images.forEach(function(node) {
			var ar = Math.round(node.naturalWidth / node.naturalHeight);
			node.classList.add('width--' + ar);
			this.grid.add(ar, node);
		}.bind(this));
		this.grid.slots.forEach(function(slot) {
			var row = document.createElement('div');
			row.className = 'image-row';
			container.appendChild(row);
			slot.forEach(function(node) { row.appendChild(node); });
			this.rows.push(row);
		}.bind(this));
	}
});

/*
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
*/

App.instance.addChild('editorial', new Editorial());

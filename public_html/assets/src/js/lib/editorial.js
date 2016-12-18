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


App.instance.addChild('editorial', new Editorial());

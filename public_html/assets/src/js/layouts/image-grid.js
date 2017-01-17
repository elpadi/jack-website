function ImageGrid() {
}

Object.defineProperty(ImageGrid.prototype, 'init', {
	value: function init() {
		this.container = $('.image-grid').filter(function(i, node) { return node.offsetWidth; });
		this.items = this.container.find('.image-grid__item');
	}
});

Object.defineProperty(ImageGrid.prototype, 'organize', {
	value: function organize() {
		var colCount = this.container.data('colCount');
		var items = this.items.get().map(function(node, i) {
			return {
				node: node,
				width: parseInt(node.dataset.width, 10),
				index: i
			};
		});
		// 1. sort by width
		items.sort(function(a, b) { return b.width - a.width; });
		var widths = _.pluck(items, 'width');
		// 2. insert into rows
		var rows = (function() {
			var rows = [], taken = [];
			items.forEach(function(item, index) {
				var total = widths[index], row = [index], tries = 0, i,
					avail = _.difference(_.range(0, items.length), taken);
				if (taken.includes(index)) return true;
				taken.push(index);
				while (total < colCount && tries < 20) {
					tries++;
					i = shuffle.pick(avail);
					if (taken.includes(i)) continue;
					if (widths[i] + total <= colCount) {
						row.push(i);
						taken.push(i);
						total += widths[i];
					}
				}
				console.log('organize', 'row', row, 'width', total);
				// 2.1 - shuffle row items
				shuffle(row);
				rows.push(row);
			});
			return rows;
		})();
		// 3 - order rows by length
		rows.sort(function(a, b) { return b.length - a.length; });
		// 4 - insert rows
		rows.forEach(function(row) {
			var node = document.createElement('div'), ar = 0;
			node.className = 'image-grid__row';
			this.container.append(node);
			row.forEach(function(index) {
				node.appendChild(items[index].node);
				ar += parseFloat(items[index].node.dataset.ar);
			});
			node.style.height = (100 / ar).toFixed(2) + 'vw';
		}.bind(this));
	}
});

Object.defineProperty(ImageGrid.prototype, 'resize', {
	value: function resize() {
	}
});

Object.defineProperty(ImageGrid.prototype, 'ancestorItem', {
	value: function ancestorItem(node) {
		if (node.classList.contains('image-grid__item')) return node;
		var parentNode = node.parentNode;
		while (parentNode.nodeName !== 'ARTICLE') parentNode = parentNode.parentNode;
		return parentNode;
	}
});


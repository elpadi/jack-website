class GridRow {

	constructor() {
		this.node = document.createElement('div');
		this.node.className = 'grid-row';
		this.total = 0;
		this.items = [];
	}

	addItem(item) {
		this.items.push(item);
		this.total += item.ratio;
		this.node.appendChild(item.node);
		this.node.style.gridTemplateColumns += ` ${item.ratio.toFixed(3)}fr `;
		item.node.innerHTML = '';
		item.createImage();
	}

	setFixedHeight(h) {
		let widths = this.items.map(item => Math.round(item.ratio * h));
		this.node.style.gridTemplateColumns = widths.map(w => w + 'px').join(' ');
	}

}

class GridItem {

	constructor(node) {
		this.node = node;
		let size = node.dataset.size.split('x').map(n => Number(n));
		this.ratio = size[0] / size[1];
		node.style.transitionDelay = (500 + Math.floor(Math.random() * 3000)) + 'ms';
	}

	createImage() {
		this.img = new Image();
		this.node.appendChild(this.img);
	}

	setSource() {
		let d = this.node.dataset;
		this.img.srcset = `${d.src} 1x, ${d.srcset}`;
	}

}

class ImageGrid {

	constructor(node) {
		this.node = node;
		this.rows = [];
		this.items = Array.from(node.querySelectorAll('a')).map(a => new GridItem(a));
		this.colCount = this.getColumnCount();
		this.loadedCount = 0;
	}

	init() {
		this.addItems();
		setTimeout(this.showItems.bind(this), 100);
	}

	getColumnCount() {
		let sizes = [1280, 980, 640, 480], w = window.innerWidth, min = 2;
		let i = sizes.findIndex(s => w >= s);
		return i == -1 ? min : sizes.length + min - i;
	}

	getNextRow(ratio) {
		let i = this.rows.findIndex(r => r.total + ratio < this.colCount);
		return i == -1 ? this.newRow() : this.rows[i];
	}

	newRow() {
		let r = new GridRow();
		this.rows.push(r);
		this.node.appendChild(r.node);
		return r;
	}

	getAvgRowHeight() {
		let c = this.rows.length;
		return (this.node.offsetHeight - c * ImageGrid.GRID_GAP) / c;
	}

	onImageLoad() {
		this.loadedCount++;
		if (this.loadedCount >= this.items.length) this.node.classList.add('grid--ready');
	}

	showItems() {
		this.rows[this.rows.length - 1].setFixedHeight(this.getAvgRowHeight());
		for (const item of this.items) {
			item.img.addEventListener('load', this.onImageLoad.bind(this));
			item.setSource();
		}
	}

	addItems() {
		for (const item of this.items) this.getNextRow(item.ratio).addItem(item);
	}

}

ImageGrid.GRID_GAP = 0;

App.instance.addChild('imageGrid', new ImageGrid(document.querySelector('.image-grid')));

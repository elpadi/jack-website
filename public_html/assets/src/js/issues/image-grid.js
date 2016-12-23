function LayoutGrid() {
	ImageGrid.call(this);
	this.HEX_COLOR_VALUES = String.fromCharCode.apply(this, _.range(65, 71)) + _.range(0, 10).join('');
}

LayoutGrid.prototype = Object.create(ImageGrid.prototype);
LayoutGrid.prototype.constructor = LayoutGrid;

Object.defineProperty(LayoutGrid.prototype, 'init', {
	value: function init() {
		ImageGrid.prototype.init.call(this);
		this.resize();
		this.loadImage(this.active.find('article').get(0));
	}
});

Object.defineProperty(LayoutGrid.prototype, 'loadImage', {
	value: function loadImage(item) {
		var node = _.head(item.getElementsByTagName('a')), img = new Image();
		img.addEventListener('load', _.bind(this.onImageLoad, this, item));
		img.addEventListener('load', _.bind(item.nextElementSibling ? this.loadImage : this.onLoadEnd, this, item.nextElementSibling));
		img.src = node.dataset.image;
		img.title = node.innerHTML;
		img.alt = '';
		node.childNodes[0].remove();
		node.appendChild(img);
		item.style.backgroundColor = '#' + this.randomHexColor();
	}
});

Object.defineProperty(LayoutGrid.prototype, 'randomHexColor', {
	value: function randomHexColor() {
		for (var i = 0, l = this.HEX_COLOR_VALUES.length, c = ''; i < 6; i++) c += this.HEX_COLOR_VALUES[_.random(0, l - 1)];
		return c;
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onLoadEnd', {
	value: function onLoadEnd() {
		this.organize();
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onImageLoad', {
	value: function onImageLoad(item, e) {
		var ar = e.target.naturalWidth / e.target.naturalHeight;
		item.dataset.width = Math.round(ar);
		item.dataset.ar = ar.toFixed(2);
	}
});

Object.defineProperty(LayoutGrid.prototype, 'resize', {
	value: function resize() {
		var size = window.innerWidth > 980 ? 'medium' : 'small';
		this.active = this.container.filter(function(i, node) { return node.dataset.size == size; });
	}
});

App.instance.addChild('layouts', new LayoutGrid());

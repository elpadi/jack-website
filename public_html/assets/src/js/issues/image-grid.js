function LayoutGrid() {
	ImageGrid.call(this);
}

LayoutGrid.prototype = Object.create(ImageGrid.prototype);
LayoutGrid.prototype.constructor = LayoutGrid;

Object.defineProperty(LayoutGrid.prototype, 'init', {
	value: function init() {
		ImageGrid.prototype.init.call(this);
		this.resize();
		this.loadImage(this.container.find('article').get(0));
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
		item.style.backgroundColor = App.instance.randomColor();
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onLoadEnd', {
	value: function onLoadEnd() {
		this.organize();
		this.items = this.container.find('.image-grid__item'); // refresh to update order
		this.container.css('opacity', '1');
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onImageLoad', {
	value: function onImageLoad(item, e) {
		var ar = e.target.naturalWidth / e.target.naturalHeight;
		item.dataset.width = Math.round(ar);
		item.dataset.ar = ar.toFixed(2);
	}
});

App.instance.addChild('layouts', new LayoutGrid());

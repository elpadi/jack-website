function ImageRow() {
	this.resize = this.matchHeights.bind(this);
	this.load = this.matchHeights.bind(this);
}

Object.defineProperty(ImageRow.prototype, 'init', {
	value: function init() {
	}
});

Object.defineProperty(ImageRow.prototype, 'matchHeights', {
	value: function matchHeights() {
		$('.image-row').each(function(i, node) {
			var h = 100 / _.map(node.children, function(img) {
				return img.naturalWidth / img.naturalHeight;
			}).reduce(function(a, b) {
				return a + b;
			}, 0);
			node.style.height = Math.floor(node.offsetWidth * h / 100) + 'px';
			//node.style.height = Math.floor(h) + 'vw';
		});
	}
});

App.instance.addChild('image_row', new ImageRow());

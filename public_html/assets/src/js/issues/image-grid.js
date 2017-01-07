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
		this.container.on('click', 'a', this.onLayoutClick.bind(this));
	}
});

Object.defineProperty(LayoutGrid.prototype, 'showLayout', {
	value: function showLayout(data) {
		var img = new Image(),
			container = document.createElement('div');
		img.src = data.layout.src;
		img.srcset = data.layout.srcset;
		img.alt = '';
		img.sizes = '100vw';
		App.instance.respImageMaxWidth(img);
		container.className = 'modal-content modal-layout';
		container.appendChild(img);
		$(document.createElement('a'))
			.attr('href', data.layout.editorial_url)
			.addClass($('#info-icon').attr('class'))
			.append($('#info-icon').children().clone())
			.appendTo(container);
		console.log(data.section);
		App.instance.loadModal.removeClass('loading').append(container);
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onLayoutClick', {
	value: function onLayoutClick(e) {
		e.preventDefault();
		App.instance.loadingModal();
		App.instance.fetch(e.currentTarget.href)
			.then(function(response) { return response.json(); })
			.then(this.showLayout.bind(this));
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

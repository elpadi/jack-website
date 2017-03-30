App.instance.addChild('slideshow', new LayoutSlideshow());

function LayoutSlideshow() {
	this.DURATION = 4000;
	this.DELAY = 2000;
	this.INITIAL_INDEX = 0;
}

Object.defineProperty(LayoutSlideshow.prototype, 'init', {
	value: function init() {
		console.log('hello');
		App.instance.fetch(location.href.substr(0, location.href.lastIndexOf('/')))
			.then(this.createElements.bind(this));
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'createElements', {
	value: function createElements(data) {
		var div = document.createElement('div'),
				slug = location.href.split('/').pop();
		div.id = 'slideshow';
		data.layouts.forEach(function(layout, index) {
			var img = new Image(), d = document.createElement('div');
			img.dataset.lazy = App.BASE_URL + layout.image.path;
			d.appendChild(img);
			div.appendChild(d);
			if (layout.slug === slug) this.INITIAL_INDEX = index;
		}.bind(this));
		document.getElementById('content').appendChild(div);
		setTimeout(this.initSlick.bind(this), 100);
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'initSlick', {
	value: function initSlick() {
		var slick = $('#slideshow').slick({
			lazyLoad: 'ondemand',
			initialSlide: this.INITIAL_INDEX
		}).slick('getSlick');
		setTimeout(function() {
			['prev','next'].forEach(function(s) {
				slick['$' + s + 'Arrow'].html('').addClass('svg-icon icon--arrow icon--' + s).append($('#arrow-icon').find('svg').clone());
			});
		}, 100);
	}
});


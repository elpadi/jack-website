App.instance.addChild('slideshow', new LayoutSlideshow());

function LayoutSlideshow() {
	this.DURATION = 4000;
	this.DELAY = 2000;
	this.INITIAL_INDEX = 0;
	this.VERTICAL_PADDING = 65;
}

Object.defineProperty(LayoutSlideshow.prototype, 'init', {
	value: function init() {
		App.instance.fetch(location.href.substr(0, location.href.lastIndexOf('/')))
			.then(this.createElements.bind(this));
		this.setupShareButtons();
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'createElements', {
	value: function createElements(data) {
		var div = document.createElement('div'), w = window.innerWidth, h = window.innerHeight - this.VERTICAL_PADDING,
				slug = location.href.split('/').pop();
		div.id = 'slideshow';
		data.layouts.forEach(function(layout, index) {
			var img = new Image(), d = document.createElement('div');
			img.dataset.lazy = App.BASE_URL + layout.image.path;
			d.style.width = w + 'px';
			d.style.height = h + 'px';
			d.appendChild(img);
			div.appendChild(d);
			if (layout.slug === slug) this.INITIAL_INDEX = index;
		}.bind(this));
		document.getElementById('content').appendChild(div);
		setTimeout(this.initSlick.bind(this), 100);
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'mousemove', {
	value: function mousemove(e) {
		if (e.screenX > window.innerWidth / 2) {
			document.body.classList.add('mouse--right');
			document.body.classList.remove('mouse--left');
		}
		else {
			document.body.classList.add('mouse--left');
			document.body.classList.remove('mouse--right');
		}
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'initSlick', {
	value: function initSlick() {
		this.slick = $('#slideshow').slick({
			lazyLoad: 'ondemand',
			initialSlide: this.INITIAL_INDEX
		})
		.on('click', this.onSlideshowClick.bind(this))
		.slick('getSlick');
		$('#slideshow-controls').appendTo('#content').on('click', 'button', this.onControlButtonClick.bind(this));
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'onSlideshowClick', {
	value: function onSlideshowClick(e) {
		if (e.screenX > window.innerWidth / 2) {
			this.next();
		}
		else {
			this.prev();
		}
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'onControlButtonClick', {
	value: function onControlButtonClick(e) {
		var id = e.currentTarget.id;
		if ((id in this) && (typeof(this[id]) === 'function')) this[id].call(this);
		else console.warn('Control button not implemented:', id);
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'prev', { value: function prev() { this.slick.slickPrev(); } });
Object.defineProperty(LayoutSlideshow.prototype, 'next', { value: function next() { this.slick.slickNext(); } });

Object.defineProperty(LayoutSlideshow.prototype, 'thumbs', {
	value: function thumbs() {
		if (App.HTTP_REFERER.indexOf('thejackmag.com/issues/dare-to-dream/layouts/part-') > 0) history.back();
		else location = document.getElementById('thumbs').dataset.url;
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'fullscreen', {
	value: function fullscreen() {
		return App.instance.isFullscreen() ? App.instance.exitFullscreen() : App.instance.goFullscreen(document.getElementById('content'));
		setTimeout(this.slick.setPosition.bind(this.slick), 500);
	}
});

Object.defineProperty(LayoutSlideshow.prototype, 'setupShareButtons', {
	value: function setupShareButtons() {
		document.getElementById('facebook').href += '?u=' + encodeURIComponent(location.href);
	}
});

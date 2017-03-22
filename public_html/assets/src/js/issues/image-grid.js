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
		if (!App.IS_PHONE) this.container.on('click', 'a', this.onLayoutClick.bind(this));
	}
});

Object.defineProperty(LayoutGrid.prototype, 'createArrowButton', {
	value: function createArrowButton(name) {
		return $('#arrow-icon').clone().attr('id', '').addClass('icon--' + name)
			.on('click',  _.bind(this.selectLayout, this, $(this.items[(this.currentIndex + this.items.length + (name === 'next' ? 1 : -1)) % this.items.length])));
	}
});

Object.defineProperty(LayoutGrid.prototype, 'createInfoButton', {
	value: function createInfoButton(url) {
		return $(document.createElement('a'))
			.attr('href', url)
			.addClass($('#info-icon').attr('class'))
			.append($('#info-icon').children().clone());
	}
});

Object.defineProperty(LayoutGrid.prototype, 'showLayout', {
	value: function showLayout(data) {
		console.log('showLayout', data.layout.slug, App.instance.time());
		var img = new Image(),
			container = document.createElement('div');
		img.src = data.layout.src;
		img.srcset = data.layout.srcset;
		img.alt = '';
		img.sizes = '100vw';
		App.instance.respImageMaxWidth(img);
		container.className = 'modal-content modal-layout';
		container.appendChild(img);
		this.createInfoButton(data.layout.editorial_url).appendTo(container);
		this.createArrowButton('prev').prependTo(container);
		this.createArrowButton('next').appendTo(container);
		App.instance.loadModal.removeClass('loading').append(container);
	}
});

Object.defineProperty(LayoutGrid.prototype, 'hide', {
	value: function hide(switching) {
		console.log('hide', switching, App.instance.time());
		if (switching) {
			if (this.currentIndex >= 0) {
				App.instance.loadModal.css('opacity', '0');
				setTimeout(function() { App.instance.loadModal.find('.modal-content').remove(); }, App.MODAL_FADE_DURATION);
				return App.instance.delayPromise(App.MODAL_FADE_DURATION + 100);
			}
			else {
				return Promise.resolve(-1);
			}
		}
		else {
			console.error('Not implemented');
		}
	}
});

Object.defineProperty(LayoutGrid.prototype, 'selectLayout', {
	value: function selectLayout(item, url) {
		console.log('selectLayout', this.currentIndex, item);
		if (!item) {
			return this.hide();
		}
		if (typeof url !== 'string') url = item.find('a').attr('href');
		Promise.all([App.instance.fetch(url), this.hide(true)])
			.then(function(values) {
				if (this.currentIndex >= 0) setTimeout(function() { App.instance.loadModal.css('opacity', ''); }, 100);
				this.currentIndex = item.index();
				this.showLayout(values[0]);
			}.bind(this));
	}
});

Object.defineProperty(LayoutGrid.prototype, 'onLayoutClick', {
	value: function onLayoutClick(e) {
		e.preventDefault();
		App.instance.loadingModal().one('hidden', function() { this.currentIndex = -1; }.bind(this));
		this.selectLayout($(e.currentTarget).closest('article'), e.currentTarget.href);
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

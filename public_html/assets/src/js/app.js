document.documentElement.className = 'js';

function App() {
	this.children = {};
	this.HEX_COLOR_VALUES = String.fromCharCode.apply(window, _.range(65, 71)) + _.range(0, 10).join('');
}

Object.defineProperty(App.prototype, 'dispatchEvent', {
	value: function dispatchEvent(name) {
		var params = Array.from(arguments).slice(1), listeners;
		console.log('App.dispatchEvent', name, params);
		if (name in this) this[name].apply(this, params);
		for (var key in this.children) {
			listeners = Array.isArray(this.children[key]) ? this.children[key] : [this.children[key]];
			listeners.filter(function(listener) { return (name in listener) && typeof(listener[name]) === 'function'; }).forEach(function(listener) {
				console.log('App.dispatchEvent', key, name, params);
				listener[name].apply(listener, params || []);
			});
		}
	}
});

Object.defineProperty(App.prototype, 'addChild', {
	value: function addChild(name, obj) {
		if (name in this.children) throw "Name already taken.";
		this.children[name] = obj;
	}
});

Object.defineProperty(App.prototype, 'enableScrollEvent', {
	value: function enableScrollEvent() {
		$(window).on('scroll', function() {
			App.instance.dispatchEvent('scroll', ('scrollY' in window) ? window.scrollY : document.body.scrollTop);
		});
	}
});

Object.defineProperty(App.prototype, 'respImageMaxWidth', {
	value: function respImageMaxWidth(img) {
		var maxWidth = parseInt(img.srcset.split(', ').pop().replace(/.* ([0-9]+)w/, '$1'), 10);
		if (!isNaN(maxWidth)) img.style.maxWidth = maxWidth + 'px';
	}
});

Object.defineProperty(App.prototype, 'onModalHide', {
	value: function onModalHide() {
		this.loadModal.find('.modal-content').remove();
	}
});

Object.defineProperty(App.prototype, 'loadingModal', {
	value: function loadingModal() {
		if (!('loadModal' in this)) {
			this.loadModal = $(document.createElement('div'))
				.append(
					$(document.createElement('div'))
						.addClass('loader-inner ball-pulse')
						.loaders()
				)
				.addClass('modal fade loading')
				.appendTo(document.body)
				.modal()
				.on('click', '.modal-content', function(e) {
					if (e.target.classList.contains('modal-content')) this.loadModal.modal('hide');
				}.bind(this))
				.on('hidden', this.onModalHide.bind(this));
		}
		else this.loadModal.addClass('loading').modal('show');
		return this.loadModal;
	}
});

Object.defineProperty(App.prototype, 'randomColor', {
	value: function randomHexColor() {
		for (var i = 0, l = this.HEX_COLOR_VALUES.length, c = '#'; i < 6; i++) c += this.HEX_COLOR_VALUES[_.random(0, l - 1)];
		return c;
	}
});

Object.defineProperty(App.prototype, 'fetch', {
	value: function fetch(url) {
		var headers = new Headers();
		headers.append('Content-Type', 'application/json');
		return window.fetch(url, { headers: headers });
	}
});

Object.defineProperty(App.prototype, 'init', {
	value: function init() {
		if (Object.keys(this.children).some(function(name) {
			return ('scroll' in this.children[name]);
		}, this)) this.enableScrollEvent();
		_.filter(document.getElementsByTagName('img'), _.property('srcset')).forEach(this.respImageMaxWidth.bind(this));
	}
});

App.instance = new App();

(function($) {
	$(document).ready(function() {
		App.instance.dispatchEvent('init');
	});
	$(window).on('resize', function() {
		App.instance.dispatchEvent('resize');
	});
})(jQuery);

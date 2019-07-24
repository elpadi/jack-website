var $ = require('jquery');
var fetch = require('fetch');
var _ = require('lodash');

function App() {
	this.children = {};
	this.initialEvents = [];
	this.isListeningInitialEvents = false;
	this.START_TIME = Date.now();
}

Object.defineProperty(App, 'HEX_COLOR_VALUES', { value: String.fromCharCode.apply(window, _.range(65, 71)) + _.range(0, 10).join('') });
Object.defineProperty(App, 'IS_HANDHELD', { value: (('screen' in window) && ('orientation' in screen) && screen.orientation.angle > 0) || ('ontouchstart' in window) });
Object.defineProperty(App, 'IS_PHONE', { value: (App.IS_HANDHELD && screen.availWidth < 768) });
Object.defineProperty(App, 'IS_TABLET', { value: (App.IS_HANDHELD && !App.IS_PHONE) });
Object.defineProperty(App, 'MODAL_FADE_DURATION', { value: 200 });

Object.defineProperty(App.prototype, 'dispatchEvent', {
	value: function dispatchEvent(name) {
		var params = Array.from(arguments).slice(1), listeners;
		//console.log('App.dispatchEvent', name, params);
		if (name in this) this[name].apply(this, params);
		for (var key in this.children) {
			listeners = Array.isArray(this.children[key]) ? this.children[key] : [this.children[key]];
			listeners.filter(function(listener) { return (name in listener) && typeof(listener[name]) === 'function'; }).forEach(function(listener) {
				//console.log('App.dispatchEvent', key, name, params);
				listener[name].apply(listener, params || []);
			});
		}
	}
});

Object.defineProperty(App.prototype, 'time', {
	value: function appTime() {
		return ((Date.now() - this.START_TIME) / 1000).toFixed(1);
	}
});

Object.defineProperty(App.prototype, 'addChild', {
	value: function addChild(name, obj) {
		if (name in this.children) throw "Name already taken.";
		this.children[name] = obj;
	}
});

Object.defineProperty(App.prototype, 'enableMousemoveEvent', {
	value: function enableMousemoveEvent() {
		var mousemoveTimeoutId = 0, clearMousemove = function() { document.body.classList.remove('mousemove'); };
		if ('MOUSEMOVE_ENABLED' in this) return;
		this.MOUSEMOVE_ENABLED = true;
		window.addEventListener('mousemove', _.throttle(function(e) {
			clearTimeout(mousemoveTimeoutId);
			document.body.classList.add('mousemove');
			mousemoveTimeoutId = setTimeout(clearMousemove, 300);
			this.dispatchEvent('mousemove', e)
		}.bind(this), 200, true));
	}
});

Object.defineProperty(App.prototype, 'enableScrollEvent', {
	value: function enableScrollEvent() {
		if ('SCROLL_ENABLED' in this) return;
		this.SCROLL_ENABLED = true;
		$(window).on('scroll', function() {
			window._app.dispatchEvent('scroll', ('scrollY' in window) ? window.scrollY : document.body.scrollTop);
		});
	}
});

Object.defineProperty(App.prototype, 'respImageMaxWidth', {
	value: function respImageMaxWidth(img) {
		/*
		var maxWidth = parseInt(img.srcset.split(', ').pop().replace(/.* ([0-9]+)w/, '$1'), 10);
		if (!isNaN(maxWidth)) img.style.maxWidth = maxWidth + 'px';
		*/
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
				.addClass('modal fade loading')
				.appendTo(document.body)
				.modal()
				.on('click', function(e) {
					if (e.target.classList.contains('modal-content') || !this.loadModal.find('.modal-content').get(0).contains(e.target)) this.loadModal.modal('hide');
				}.bind(this))
				.on('hidden', this.onModalHide.bind(this));
		}
		else this.loadModal.addClass('loading').modal('show');
		return this.loadModal;
	}
});

Object.defineProperty(App.prototype, 'randomColor', {
	value: function randomHexColor() {
		for (var i = 0, l = App.HEX_COLOR_VALUES.length, c = '#'; i < 6; i++) c += App.HEX_COLOR_VALUES[_.random(0, l - 1)];
		return c;
	}
});

Object.defineProperty(App.prototype, 'loadPromise', {
	value: function loadPromise(data) {
		var img = new Image();
		return new Promise(function(resolve, reject) {
			if (!('src' in data)) reject('No src specified');
			img.addEventListener('load', function(e) { resolve(img); });
			if ('srcset' in data) {
				img.srcset = data.srcset;
				window._app.respImageMaxWidth(img);
			}
			img.src = data.src;
			img.alt = '';
		});
	}
});

Object.defineProperty(App.prototype, 'delayPromise', {
	value: function delayPromise(delay) {
		var id;
		var p = new Promise(function(resolve, reject) {
			id = setTimeout(resolve, delay);
		});
		p.timeoutId = id;
		return p;
	}
});

Object.defineProperty(App.prototype, 'submitForm', {
	value: function submitForm(form) {
		var data = new FormData(form);
		return this.fetch(form.action, {
			method: form.method,
			body: $(form).serialize()
		}, {
			"Content-Type": 'application/x-www-form-urlencoded'
		});
	}
});

Object.defineProperty(App.prototype, 'fetch', {
	value: function fetch(url, extraOptions, extraHeaders) {
		var headers = { "Accept": 'application/json' };
		var options = { headers: headers, credentials: 'include' };
		if (arguments.length > 2) Object.keys(extraHeaders).forEach(function(key) {
			headers[key] = extraHeaders[key];
		});
		if (arguments.length > 1) Object.keys(extraOptions).forEach(function(key) {
			options[key] = extraOptions[key];
		});
		return (('fetch' in window) ? window.fetch : fetch)(url, options).then(function(response) {
			if (response.ok) return response.json();
			console.error("Fetching of resource failed.", response);
		});
	}
});

Object.defineProperty(App.prototype, 'isFullscreen', {
	value: function isFullscreen() {
		return ['fullscreenElement','webkitFullscreenElement','mozFullScreenElement','msFullscreenElement','webkitFullscreenElement','webkitFullscreenElement'].some(function(prop) {
			return (prop in document) && document[prop];
		});
	}
});

Object.defineProperty(App.prototype, 'goFullscreen', {
	value: function goFullscreen(node) {
		var fn = ['requestFullscreen','webkitRequestFullscreen','mozRequestFullScreen','msRequestFullscreen','webkitRequestFullscreen','webkitRequestFullscreen'].filter(function(fn) {
			return (fn in node);
		});
		if (fn.length) {
			node[fn[0]].call(node);
			document.body.classList.add('fullscreen');
		}
	}
});

Object.defineProperty(App.prototype, 'exitFullscreen', {
	value: function exitFullscreen() {
		var fn = ['exitFullscreen','webkitExitFullscreen','mozCancelFullScreen','msExitFullscreen','webkitExitFullscreen','webkitExitFullscreen'].filter(function(fn) {
			return (fn in document);
		});
		if (fn.length) {
			document[fn[0]].call(document);
			document.body.classList.remove('fullscreen');
		}
	}
});

Object.defineProperty(App.prototype, 'updateBackgrounds', {
	value: function updateBackgrounds() {
		Array.from(document.getElementsByClassName('background')).forEach(function(bg) {
			var container = bg.classList.contains('page-background') ? document.body : (bg.parentNode.classList.contains('background-canvas') ? bg.parentNode : null);
			if (container) container.style.backgroundImage = 'url(' + (bg.currentSrc || bg.src) + ')';
		});
	}
});

Object.defineProperty(App.prototype, 'init', {
	value: function init() {
		if (!App.IS_PHONE) this.updateBackgrounds();
		Object.keys(this.children).forEach(function(name) {
			if ('scroll' in this.children[name]) this.enableScrollEvent();
			if ('mousemove' in this.children[name]) this.enableMousemoveEvent();
		}.bind(this));
		_.filter(document.getElementsByTagName('img'), _.property('srcset')).forEach(this.respImageMaxWidth.bind(this));

		this.initialEvents.push('init');
		this.scrollData = {
			before: window.scrollY
		};
		this.addEventListener('click');
		this.addEventListener('resize', undefined, window);
		this.addEventListener('scroll', undefined, window);
	}
});

Object.defineProperty(App.prototype, 'load', {
	value: function load() {
		this.initialEvents.push('load');
		this.initialEvents.push('resize');
		document.body.className += ' content-loaded';
		setTimeout(function() { this.dispatchEvent('resize'); }.bind(this), 100);
		setTimeout(function() { this.dispatchEvent('scroll'); }.bind(this), 200);
	}
});

Object.defineProperty(App.prototype, 'bootstrap', {
	value: function bootstrap() {
		if (this.isListeningInitialEvents) return;
		console.log('App.bootstrap', document.readyState);
		switch (document.readyState) {
		case 'complete':
			this.dispatchEvent('init');
			this.dispatchEvent('load');
			break;
		case 'interactive':
			this.dispatchEvent('init');
			this.addEventListener('load', undefined, window);
			break;
		default:
			this.addEventListener('DOMContentLoaded', 'init');
			this.addEventListener('load', undefined, window);
		}
		this.isListeningInitialEvents = true;
	}
});

Object.defineProperty(App.prototype, 'addEventListener', {
	value: function addEventListener(domEvent, appFn, dispatcher) {
		(dispatcher || document).addEventListener(domEvent, function(e) { this.dispatchEvent(appFn || domEvent, e); }.bind(this));
	}
});

Object.defineProperty(App.prototype, 'resize', {
	value: function resize() {
		this.dimensions = {
			width: document.documentElement.clientWidth,
			height: document.documentElement.clientHeight
		};
	}
});

Object.defineProperty(App.prototype, 'scroll', {
	value: function scroll(e) {
		if (e && e.target !== document && e.target !== window) return;
		if (this.scrollData.before === window.scrollY) this.scrollData.before = 0;
		this.scrollData.current = window.scrollY;
	}
});

Object.defineProperty(App.prototype, 'click', {
	value: function click(e) {
		console.log('App.click', e.target);
	}
});

module.exports = App;

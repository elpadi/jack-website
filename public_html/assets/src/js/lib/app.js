Object.defineProperty(App.prototype, 'bootstrap', {
	value: function bootstrap() {
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
	}
});

Object.defineProperty(App.prototype, 'addEventListener', {
	value: function addEventListener(domEvent, appFn, dispatcher) {
		(dispatcher || document).addEventListener(domEvent, function(e) { this.dispatchEvent(appFn || domEvent, e); }.bind(this));
	}
});

Object.defineProperty(App.prototype, 'init', {
	value: function init() {
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

App.instance.bootstrap();

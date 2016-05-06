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
		this.addEventListener('click');
		this.addEventListener('resize', undefined, window);
	}
});

Object.defineProperty(App.prototype, 'load', {
	value: function load() {
		this.initialEvents.push('load');
		this.initialEvents.push('resize');
		document.body.className += ' content-loaded';
		setTimeout(function() { this.dispatchEvent('resize'); }.bind(this), 100);
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

Object.defineProperty(App.prototype, 'click', {
	value: function click(e) {
		console.log('App.click', e.target);
	}
});

App.instance.bootstrap();

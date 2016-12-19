document.documentElement.className = 'js';

function App() {
	this.children = {};
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

Object.defineProperty(App.prototype, 'init', {
	value: function init() {
		if (Object.keys(this.children).some(function(name) {
			return ('scroll' in this.children[name]);
		}, this)) this.enableScrollEvent();
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

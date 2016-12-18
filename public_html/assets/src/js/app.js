document.documentElement.className = 'js';

function App() {
	this.initialEvents = [];
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
		this.initialEvents.forEach(function(event) { if ((name in obj) && typeof(obj[name]) === 'function') obj[name].call(obj); });
	}
});

App.instance = new App();

(function($) {
	$(document).ready(function() {
		App.instance.dispatchEvent('init');
	});
})(jQuery);

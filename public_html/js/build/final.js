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
function Editorial() {
	this.FIXED_TOP = 190;
}

Object.defineProperty(Editorial.prototype, 'init', {
	value: function init() {
		this.fixed = document.getElementById('fixed-issue-section');
		this.sections = Array.from(document.getElementsByClassName('issue-section')).filter(function(node) { return node.id == ''; });
	}
});

Object.defineProperty(Editorial.prototype, 'resize', {
	value: function resize() {
		this.sections.forEach(function(node) {
			node.children[1].style.height = Math.max.apply(window, Array.from(node.children).map(function(childNode) { return childNode.offsetHeight; })) + 'px';
		});
	}
});

Object.defineProperty(Editorial.prototype, 'hide', {
	value: function hide(index) {
		this.fixed.children[index].classList.remove('visible');
		this.sections[index].children[0].classList.remove('hidden');
	}
});

Object.defineProperty(Editorial.prototype, 'show', {
	value: function show(index) {
		this.fixed.children[index].classList.add('visible');
		this.sections[index].children[0].classList.add('hidden');
	}
});

Object.defineProperty(Editorial.prototype, 'ahead', {
	value: function ahead(index) {
		this.hide(index);
		this.sections[index].classList.remove('behind');
	}
});

Object.defineProperty(Editorial.prototype, 'behind', {
	value: function behind(index) {
		this.hide(index);
		this.sections[index].classList.add('behind');
	}
});

Object.defineProperty(Editorial.prototype, 'scroll', {
	value: function scroll() {
		var current = NaN;
		this.sections.forEach(function(node, index) {
			if (index > current) return this.ahead(index);
			var rect = node.getBoundingClientRect();
			var fixedBottom = this.FIXED_TOP + this.sections[index].children[0].offsetHeight;
			if (rect.top <= this.FIXED_TOP && rect.bottom > fixedBottom) {
				current = index;
				this.show(index);
			}
			if (rect.top > this.FIXED_TOP) return this.ahead(index);
			if (rect.bottom <= fixedBottom) return this.behind(index);
		}.bind(this));
	}
});

App.instance.addChild('editorial', new Editorial());
function Intro() {
	this.skipped = false;
	this.timeoutIds = [];
	this.delays = {
		"posters": 5000,
		"flags": 11000,
		"logo": 17000
	};
}

Object.defineProperty(Intro.prototype, 'load', {
	value: function load(name) {
		this.show('intro-fade');
		for (var className in this.delays) this.hideOne('.' + className, this.delays[className]);
	}
});

Object.defineProperty(Intro.prototype, 'click', {
	value: function click(name) {
		if (this.skipped) return;
		while (this.timeoutIds.length) clearTimeout(this.timeoutIds.pop());
		document.body.classList.add('skip-intro');
		this.skipped = true;
	}
});

Object.defineProperty(Intro.prototype, 'show', {
	value: function show(className) {
		Array.from(document.getElementsByClassName(className)).forEach(function(node) { node.classList.add('visible'); });
	}
});

Object.defineProperty(Intro.prototype, 'hide', {
	value: function hide(className) {
		Array.from(document.getElementsByClassName(className)).forEach(function(node) { node.classList.remove('visible'); });
	}
});

Object.defineProperty(Intro.prototype, 'hideOne', {
	value: function hideOne(query, delay) {
		var node = document.querySelector(query);
		if (node) this.timeoutIds.push(setTimeout(function() { node.classList.remove('visible'); }, delay));
	}
});

App.instance.addChild('intro', new Intro());
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

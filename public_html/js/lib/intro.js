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
		this.timeoutIds.push(setTimeout(function() { document.querySelector(query).classList.remove('visible'); }, delay));
	}
});

App.instance.addChild('intro', new Intro());

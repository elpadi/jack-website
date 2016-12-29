function Intro() {
	this.DURATION = 4000;
	this.DELAY = 2000;
	this.FADES = ['posters','flags','logo'];
	this.hasEnded = false;
}

Object.defineProperty(Intro.prototype, 'init', {
	value: function init() {
		$(document).one('click', this.end.bind(this));
		App.instance.fetch(location.href)
			.then(function(response) { return response.json(); })
			.then(this.startIntro.bind(this));
	}
});

Object.defineProperty(Intro.prototype, 'end', {
	value: function end() {
		document.body.classList.add('intro-finished');
		this.hasEnded = true;
	}
});

Object.defineProperty(Intro.prototype, 'hide', {
	value: function hide() {
		var el = _.detect(this.container.children, function(node) { return node.classList.contains('visible'); });
		if (el) el.classList.remove('visible');
	}
});

Object.defineProperty(Intro.prototype, 'show', {
	value: function show(values) {
		var node = values[0];
		node.classList.add('intro-fade');
		this.container.appendChild(node);
		setTimeout(function() { node.classList.add('visible'); }, 16);
	}
});

Object.defineProperty(Intro.prototype, 'next', {
	value: function next() {
		var name = this.FADES.shift();
		if (!name || this.hasEnded) return setTimeout(this.end.bind(this), this.DURATION + this.DELAY);
		Promise.all([
			('nodeType' in this.images[name]) ? Promise.resolve(this.images[name]) : App.instance.loadPromise(this.images[name]),
			App.instance.delayPromise(name === 'posters' ? 500 : this.DURATION + this.DELAY)
		]).then(this.show.bind(this))
			.then(this.next.bind(this))
			.then(function() { return App.instance.delayPromise(this.DURATION); }.bind(this))
			.then(this.hide.bind(this));
	}
});

Object.defineProperty(Intro.prototype, 'startIntro', {
	value: function startIntro(data) {
		this.container = document.createElement('div');
		this.container.className = 'intro-container';
		document.getElementById('content').appendChild(this.container);
		this.images = data.images;
		this.images.logo = document.getElementById('masthead').getElementsByTagName('svg')[0].cloneNode(true);
		this.images.logo.id = 'intro-logo';
		this.next();
	}
});

App.instance.addChild('intro', new Intro());
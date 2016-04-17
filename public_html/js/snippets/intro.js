var Intro = {

	skipped: false,
	timeoutIds: [],

	run: function() {
		Intro.show('intro-fade');
		Intro.hideOne('.posters', 5000);
		Intro.hideOne('.flags', 11000);
		Intro.hideOne('.logo', 17000);
	},

	skip: function() {
		Intro.skipped = true;
		Intro.timeoutIds.forEach(clearTimeout);
		document.body.classList.add('skip-intro');
	},

	show: function(className) {
		Array.from(document.getElementsByClassName(className)).forEach(function(node) { node.classList.add('visible'); });
	},

	hide: function(className) {
		Array.from(document.getElementsByClassName(className)).forEach(function(node) { node.classList.remove('visible'); });
	},

	hideOne: function(query, delay) {
		Intro.timeoutIds.push(setTimeout(function() { document.querySelector(query).classList.remove('visible'); }, delay));
	},

	skipListener: function(e) {
		if (Intro.skipped) return;
		Intro.skip();
		e.currentTarget.removeEventListener(e.type, Intro.skipListener);
	}

};

window.addEventListener('load', function() {
	Intro.run();
});
document.addEventListener('click', Intro.skipListener);

function IssueSections() {
	this.scrollValues = [];
	this.active = [];
}

Object.defineProperty(IssueSections.prototype, 'init', {
	value: function init() {
		this.top = document.getElementById('issue-sections').offsetTop;
		this.sections = Array.from(document.getElementsByClassName('issue-section'));
		this.texts = Array.from(document.getElementsByClassName('section-text'));
		this.loadLayouts(this.sections[0], 0);
	}
});

Object.defineProperty(IssueSections.prototype, 'loadLayouts', {
	value: function loadLayouts(section, index) {
		var promises = Array.from(section.getElementsByClassName('section-layout')).map(function(p) {
			var promise = App.instance.loadPromise(p.dataset);
			promise.then(function(img) { p.appendChild(img); });
			return promise;
		});
		Promise.all(promises).then(function() { this.onLayoutsLoaded(section, index); }.bind(this));
	}
});

Object.defineProperty(IssueSections.prototype, 'onLayoutsLoaded', {
	value: function onLayoutsLoaded(section, index) {
		setTimeout(function() {
			this.scrollValues.push({
				top: section.offsetTop - this.top,
				max: section.offsetHeight - this.texts[index].offsetHeight
			});
			section.style.height = section.offsetHeight + 'px';
			section.classList.add('loaded');
		}.bind(this), 100);
		if (section.nextElementSibling) this.loadLayouts(section.nextElementSibling, index + 1);
		else setTimeout(this.onAllLayoutsLoaded.bind(this), 200);
	}
});

Object.defineProperty(IssueSections.prototype, 'onAllLayoutsLoaded', {
	value: function onAllLayoutsLoaded() {
		console.log(this.scrollValues);
	}
});

if (window.innerWidth >= 768) {
	Object.defineProperty(IssueSections.prototype, 'scroll', {
		value: function scroll(scrollY) {
			for (var i = 0, l = this.scrollValues.length; i < l; i++)
				$(this.texts[i]).css('transform', 'translateY(' + Math.max(0, Math.min(this.scrollValues[i].max, scrollY - this.scrollValues[i].top)) + 'px)');
		}
	});
}

App.instance.addChild('issue-sections', new IssueSections());

function SectionsScroll() {
  SynchScroll.call(this);
}

SectionsScroll.prototype = Object.create(SynchScroll.prototype);
SectionsScroll.prototype.constructor = SectionsScroll;

Object.defineProperty(SectionsScroll.prototype, 'init', {
	value: function init() {
		SynchScroll.prototype.init.call(this);
		$('.issue-sections').addClass('synch-scroll');
		this.matchFixed();
		this.issueSections = new IssueSections(this.left);
		Promise.all([App.instance.delayPromise(100), this.issueSections.fetchLayouts()]).then(function() {
			this.scrollItems = this.left.children();
			this.initPosMatch();
		}.bind(this));
		this.fixedItems = this.right.children();
	}
});

Object.defineProperty(SectionsScroll.prototype, 'resize', {
	value: function resize() {
		this.matchFixed();
	}
});

Object.defineProperty(SectionsScroll.prototype, 'matchFixed', {
	value: function matchFixed() {
		var rect = this.right.get(0).getBoundingClientRect();
		this.right.children().css({
			width: Math.round(rect.width) + 'px',
			left: Math.round(rect.left) + 'px'
		});
	}
});

function IssueSections(container) {
	this.container = container;
}

Object.defineProperty(IssueSections.prototype, 'init', {
	value: function init() {
		this.fetchLayouts();
	}
});

Object.defineProperty(IssueSections.prototype, 'onLayoutsFetchEnd', {
	value: function onLayoutsFetchEnd() {
		console.log('onLayoutsFetchEnd');
		this.resolve();
	}
});

Object.defineProperty(IssueSections.prototype, 'fetchLayout', {
	value: function fetchLayout(section, layout) {
		return App.instance.loadPromise(layout.image).then(function(img) {
			var title = document.createElement('h2');
			title.innerHTML = layout.title;
			img.alt = '';
			section.appendChild(title);
			section.appendChild(img);
			setTimeout(function() { img.style.opacity = '1'; }, 100);
			return img;
		});
	}
});

Object.defineProperty(IssueSections.prototype, 'fetchLayoutImages', {
	value: function fetchLayoutImages(slug, data) {
		var section = document.createElement('article');
		section.className = 'section__layouts synch-scroll__item';
		section.dataset.slug = slug;
		section.id = slug;
		if (!data) {
			console.warn('insertLayouts', 'no data', slug);
			return Promise.resolve(section);
		}
		return new Promise(function(resolve, reject) {
			var layouts = Array.from(data.layouts);
			var next = function next() {
				var layout = layouts.shift();
				if (layout) this.fetchLayout(section, layout).then(next.bind(this));
				else resolve(section);
			}.bind(this);
			next();
		}.bind(this));
	}
});

Object.defineProperty(IssueSections.prototype, 'fetchNextLayouts', {
	value: function fetchNextLayouts($node) {
		if ($node.length === 0) {
			this.onLayoutsFetchEnd();
			return false;
		}
		return App.instance.fetch($node.data('url'))
			.then(function(response) { return response.json(); })
			.then(_.bind(this.fetchLayoutImages, this, $node.data('slug')))
			.then(function(section) { (this.container ? this.container : $node).append(section); }.bind(this))
			.then(_.bind(this.fetchNextLayouts, this, $node.next()));
	}
});

Object.defineProperty(IssueSections.prototype, 'fetchLayouts', {
	value: function fetchLayouts(items) {
		this.fetchNextLayouts($('.issue-sections').find('article').first());
		setTimeout(function() { $('.section-texts').css('opacity', '1'); }, 100);
		return new Promise(function(resolve, reject) {
			this.resolve = resolve;
		}.bind(this));
	}
});


// Do not run on mobile devices, since position fixed can be tricky
if (!App.instance.isHandheld) App.instance.addChild('sections-scroll', new SectionsScroll());
else App.instance.addChild('issue-sections', new IssueSections());

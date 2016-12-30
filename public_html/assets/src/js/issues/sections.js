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
		this.issueSections.fetchLayouts().then(function() {
			this.scrollItems = this.left.children();
		}.bind(this));
		this.fixedItems = this.right.children();
		$(this.fixedItems[0]).css('transform', 'translateY(' + this.OFFSET_TOP + 'px)');
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

Object.defineProperty(IssueSections.prototype, 'insertLayouts', {
	value: function insertLayouts($node, slug, data) {
		var section = document.createElement('article');
		section.className = 'section-layouts synch-scroll__item';
		section.dataset.slug = slug;
		if (!data) {
			console.log('insertLayouts', 'no data', slug);
		}
		else {
			data.layouts.forEach(function(layout) {
				var image = new Image(), title = document.createElement('h2');
				title.innerHTML = layout.title;
				image.src = layout.image.src;
				image.alt = '';
				section.appendChild(title);
				section.appendChild(image);
			});
		}
		(this.container ? this.container : $node).append(section);
		return section;
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
			.then(_.bind(this.insertLayouts, this, $node, $node.data('slug')))
			.then(_.bind(this.fetchNextLayouts, this, $node.next()));
	}
});

Object.defineProperty(IssueSections.prototype, 'fetchLayouts', {
	value: function fetchLayouts(items) {
		this.fetchNextLayouts($('.issue-sections').find('article').first());
		return new Promise(function(resolve, reject) {
			this.resolve = resolve;
		}.bind(this));
	}
});


// Do not run on mobile devices, since position fixed can be tricky
if (!App.instance.isHandheld) App.instance.addChild('sections-scroll', new SectionsScroll());
else App.instance.addChild('issue-sections', new IssueSections());

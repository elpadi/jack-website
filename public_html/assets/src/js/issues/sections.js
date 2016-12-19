function IssuesSections() {
  SynchScroll.call(this);
	this.OFFSET_TOP = 150;
}

IssuesSections.prototype = Object.create(SynchScroll.prototype);
IssuesSections.prototype.constructor = IssuesSections;

Object.defineProperty(IssuesSections.prototype, 'init', {
	value: function init() {
		SynchScroll.prototype.init.call(this);
		this.fetchLayouts();
		this.matchFixed();
		$(this.fixedItems[0]).css('transform', 'translateY(' + this.OFFSET_TOP + 'px)');
	}
});

Object.defineProperty(IssuesSections.prototype, 'insertLayouts', {
	value: function insertLayouts(slug, data) {
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
		this.left.append(section);
		return section;
	}
});

Object.defineProperty(IssuesSections.prototype, 'onLayoutsFetchEnd', {
	value: function onLayoutsFetchEnd() {
		console.log('onLayoutsFetchEnd');
		this.scrollItems = this.left.children();
	}
});

Object.defineProperty(IssuesSections.prototype, 'fetchNextLayouts', {
	value: function fetchNextLayouts(headers, $node) {
		if ($node.length === 0) {
			this.onLayoutsFetchEnd();
			return false;
		}
		return fetch($node.data('url'), { headers: headers })
			.then(function(response) { return response.json(); })
			.then(_.bind(this.insertLayouts, this, $node.data('slug')))
			.then(_.bind(this.fetchNextLayouts, this, headers, $node.next()));
	}
});

Object.defineProperty(IssuesSections.prototype, 'fetchLayouts', {
	value: function fetchLayouts() {
		var headers = new Headers();
		this.fixedItems = this.right.children();
		headers.append('Content-Type', 'application/json');
		this.fetchNextLayouts(headers, this.fixedItems.first());
	}
});

Object.defineProperty(IssuesSections.prototype, 'resize', {
	value: function resize() {
		this.matchFixed();
	}
});

Object.defineProperty(IssuesSections.prototype, 'matchFixed', {
	value: function matchFixed() {
		var rect = this.right.get(0).getBoundingClientRect();
		this.right.children().css({
			width: Math.round(rect.width) + 'px',
			left: Math.round(rect.left) + 'px'
		});
	}
});

App.instance.addChild('issues-sections', new IssuesSections());

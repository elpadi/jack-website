function IssuesSections() {
  SynchScroll.call(this);
	console.log('issues-sections');
}

IssuesSections.prototype = Object.create(SynchScroll.prototype);
IssuesSections.prototype.constructor = IssuesSections;

Object.defineProperty(IssuesSections.prototype, 'init', {
	value: function init() {
		SynchScroll.prototype.init.call(this);
		this.fetchLayouts();
	}
});

Object.defineProperty(IssuesSections.prototype, 'fetchLayouts', {
	value: function fetchLayouts() {
		this.items.each(function(i, node) {
			console.log(node.dataset.url);
		});
	}
});

App.instance.addChild('issues-sections', new IssuesSections());

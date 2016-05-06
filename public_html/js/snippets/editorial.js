App.instance.addChild('editorial', {
	resize: function() {
		console.log('editorial', 'resize', document.getElementsByClassName('issue-section'));
		Array.from(document.getElementsByClassName('issue-section')).forEach(function(node) {
			node.children[1].style.height = Math.max.apply(window, Array.from(node.children).map(function(childNode) { return childNode.offsetHeight; })) + 'px';
		});
	}
});

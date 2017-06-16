function Models() {
}

Object.defineProperty(Models.prototype, 'init', {
	value: function init() {
		$('.models').find('main').find('a').each(function(i, a) {
			a.setAttribute('target', '_blank');
		});
	}
});


App.instance.addChild('models', new Models());

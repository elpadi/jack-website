function Home() {
}

Object.defineProperty(Home.prototype, 'init', {
	value: function init() {
		$('.models').find('a').each(function(i, a) {
			a.setAttribute('target', '_blank');
		});
	}
});


App.instance.addChild('home', new Home());

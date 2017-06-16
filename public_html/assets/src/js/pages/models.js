function Models() {
}

Object.defineProperty(Models.prototype, 'init', {
	value: function init() {
		Array.from(document.getElementsByTagName('img')).filter(function(img) {
			return img.getAttribute('data-fashion') && img.parentNode.nodeName !== 'FIGURE';
		}).forEach(function(img) {
			var fig = document.createElement('figure'),
					cap = document.createElement('figcaption'),
					side = document.createElement('aside'),
					txt = document.createElement(img.getAttribute('data-fashion-url') ? 'a' : 'span');
			img.parentNode.insertBefore(fig, img);
			fig.appendChild(img);
			fig.appendChild(cap);
			txt.innerHTML = img.getAttribute('data-fashion');
			txt.setAttribute('href', img.getAttribute('data-fashion-url'));
			txt.setAttribute('target', '_blank');
			txt.className = 'plain';
			side.appendChild(txt);
			cap.appendChild(side);
		});
	}
});


App.instance.addChild('models', new Models());

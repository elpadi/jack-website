function Models() {
}

Object.defineProperty(Models.prototype, 'init', {
	value: function init() {
		let pc = document.querySelector('.page-content');
		pc.id = 'page-content--models';
		for (let img of pc.querySelectorAll('img')) {
			if (this.hasData(img) == false) {
				this.addData(img);
			}
		}
	}
});

Object.defineProperty(Models.prototype, 'hasData', {
	value: function hasData(img) {
		return img.getAttribute('data-fashion') && img.parentNode.nodeName !== 'FIGURE';
	}
});

Object.defineProperty(Models.prototype, 'addData', {
	value: function addData(img) {
		var fig = document.createElement('figure'),
				cap = document.createElement('figcaption'),
				side = document.createElement('aside'),
				txt = document.createElement(img.getAttribute('data-fashion-url') ? 'a' : 'span');
		//img.parentNode.insertBefore(fig, img);
		fig.appendChild(img.cloneNode(true));
		fig.appendChild(cap);
		txt.innerHTML = img.getAttribute('data-fashion');
		txt.setAttribute('href', img.getAttribute('data-fashion-url'));
		txt.setAttribute('target', '_blank');
		txt.className = 'plain';
		side.appendChild(txt);
		cap.appendChild(side);
		img.parentNode.replaceChild(fig, img);
	}
});

module.exports = Models;

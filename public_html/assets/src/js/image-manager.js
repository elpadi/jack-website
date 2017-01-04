function ImageManager() {
}

Object.defineProperty(ImageManager.prototype, 'init', {
	value: function init() {
		window.addEventListener('click', function(e) {
			if (e.target.nodeName === 'BUTTON') this[e.target.getAttribute('href')](e);
		}.bind(this));
		document.getElementById('image-info-form').addEventListener('submit', this.imageInfo.bind(this));
	}
});

Object.defineProperty(ImageManager.prototype, 'imageInfo', {
	value: function imageInfo(e) {
		e.preventDefault();
		if ('info' in this) this.info.dom.table.remove();
		App.instance.fetch(location.href + '/' + document.getElementById('size').value + '/' + document.getElementById('path').value.replace(/\//g, '_'))
			.then(function(response) { return response.json(); })
			.then(function(data) {
				this.info = new Table({
					id: 'info',
					columns: ['hash','width','height'],
					data: ['meta','size'].map(function(key) { return [data[key].hash, data[key].width, data[key].height]; })
				});
				document.getElementById('image-info').appendChild(this.info.dom.table);
			});
	}
});

Object.defineProperty(ImageManager.prototype, 'list', {
	value: function list() {
		if ('listing' in this) this.listing.dom.table.remove();
		App.instance.fetch(location.href + '/listing')
			.then(function(response) { return response.json(); })
			.then(function(data) {
				this.listing = new Table({
					id: 'listing',
					columns: ['hash','width','height'],
					data: Object.keys(data.meta).map(function(hash) { return [hash, data.meta[hash].width, data.meta[hash].height]; })
				});
				document.getElementById('listing').appendChild(this.listing.dom.table);
			}.bind(this));
	}
});

App.instance.addChild('image-manager', new ImageManager());

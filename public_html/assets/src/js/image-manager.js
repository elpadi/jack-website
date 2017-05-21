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
			.then(function(data) {
				this.info = new Table({
					id: 'info',
					columns: ['hash','width','height'],
					data: ['meta','size'].map(function(key) { return [data[key].hash, data[key].width, data[key].height]; })
				});
				document.getElementById('image-info').appendChild(this.info.dom.table);
			}.bind(this));
	}
});

Object.defineProperty(ImageManager.prototype, 'addInfo', {
	value: function addInfo(table) {
		var th = document.createElement('td'),
			td = document.createElement('td'),
			button = document.createElement('a');
		th.innerHTML = 'info';
		button.innerHTML = 'info';
		table.thead.children[0].appendChild(th);
		td.appendChild(button);
		table.rows.forEach(function(tr) {
			var cell = td.cloneNode(true);
			cell.children[0].href = location.href + '/info?path=' + encodeURIComponent(tr.children[0].innerHTML);
			tr.appendChild(cell);
		});
	}
});

Object.defineProperty(ImageManager.prototype, 'list', {
	value: function list() {
		if ('listing' in this) this.listing.dom.table.remove();
		App.instance.fetch(location.href + '/listing')
			.then(function(data) {
				this.listing = new Table({
					id: 'listing',
					columns: ['path'],
					data: data.images.map(function(path) { return [path]; })
				});
				document.getElementById('listing').appendChild(this.listing.dom.table);
				this.addInfo(this.listing.dom);
			}.bind(this));
	}
});

App.instance.addChild('image-manager', new ImageManager());

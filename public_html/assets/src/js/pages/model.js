var InfiniteScroll = require('../layouts/infinite-scroll');

function ModelScroll() {
	InfiniteScroll.call(this);
	this.onDisplay = [];
	if (location.pathname.includes('jack-black-pussy-cat')) this.URL = location.pathname;
}

ModelScroll.prototype = Object.create(InfiniteScroll.prototype);
ModelScroll.prototype.constructor = ModelScroll;

Object.defineProperty(ModelScroll.prototype, 'init', {
	value: function init() {
		InfiniteScroll.prototype.init.call(this);
	}
});

Object.defineProperty(ModelScroll.prototype, 'fetch', {
	value: function fetch() {
		this.onDisplay = Array.from(document.getElementsByClassName('model')).map(function(model) {
			return model.dataset.slug;
		});
		return window._app.fetch(this.URL + '?action=more&exclude=' + encodeURIComponent(this.onDisplay.join(',')));
	}
});

Object.defineProperty(ModelScroll.prototype, 'appendModel', {
	value: function appendModel(model) {
		var models = document.getElementsByClassName('models');
		model.classList.add('fade');
		models[0].appendChild(model);
		models[0].appendChild(document.createElement('hr'));
		setTimeout(function() {
			model.classList.add('visible');
			this.isLoading = false;
		}.bind(this), 200);
	}
});

Object.defineProperty(ModelScroll.prototype, 'addContent', {
	value: function addContent(data) {
		var container = document.createElement('div');
		if (data.content == '') this.destroy();
		container.innerHTML = data.content;
		setTimeout(function() {
			Array.from(container.getElementsByClassName('model')).forEach(this.appendModel.bind(this));
		}.bind(this), 64);
	}
});

module.exports = ModelScroll;

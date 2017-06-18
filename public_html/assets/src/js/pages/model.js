function ModelScroll() {
	InfiniteScroll.call(this);
	this.onDisplay = [];
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
		return App.instance.fetch(this.URL + '?exclude=' + encodeURIComponent(this.onDisplay.join(',')));
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
		container.innerHTML = data.content;
		setTimeout(function() {
			Array.from(container.getElementsByClassName('model')).forEach(this.appendModel.bind(this));
		}.bind(this), 64);
		if (data.count == 1) this.destroy();
	}
});

App.instance.addChild('modelscroll', new ModelScroll());

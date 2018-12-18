function InfiniteScroll() {
	this.URL = 'infinite-scroll';
	this.isLoading = false;
	this.disabled = false;
}

Object.defineProperty(InfiniteScroll.prototype, 'init', {
	value: function init() {
		this.target = document.createElement('div');
		this.target.className = 'infinite-scroll__target loader-outer';
		this.target.appendChild(App.instance.createLoader());
		document.body.appendChild(this.target);
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'destroy', {
	value: function destroy() {
		this.disabled = true;
		this.target.remove();
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'addContent', {
	value: function addContent(data) {
		throw "This is an abstract method which must be overriden.";
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'fetch', {
	value: function fetch() {
		return App.instance.fetch(this.URL);
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'onResponse', {
	value: function onResponse(data) {
		this.target.classList.remove('loading');
		this.addContent(data);
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'next', {
	value: function next() {
		this.isLoading = true;
		this.target.classList.add('loading');
		this.fetch().then(this.onResponse.bind(this));
	}
});

Object.defineProperty(InfiniteScroll.prototype, 'scroll', {
	value: function scroll(scrollTop) {
		if (this.isLoading || this.disabled) return;
		var rect = this.target.getBoundingClientRect();
		if (rect.top < window.innerHeight) this.next();
	}
});

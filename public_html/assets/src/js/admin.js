if (!('repeater' in App.Utils.renderer)) {
	Object.defineProperty(App.Utils.renderer, 'repeater', {
		value: function renderRepeaterValue(v) {
			return v.map(function(item) { return App.Utils.renderValue(item.field.type, item.value); }).join(', ');
		}
	});
}

if (!('collectionlink' in App.Utils.renderer)) {
	Object.defineProperty(App.Utils.renderer, 'collectionlink', {
		value: function renderCollectionLinkValue(v) {
			return v.display;
		}
	});
}

if (!('set' in App.Utils.renderer)) {
	Object.defineProperty(App.Utils.renderer, 'set', {
		value: function renderCollectionLinkValue(v) {
			return Object.keys(v).map(function(k) { return v[k]; }).join(' / ');
		}
	});
}

App.Utils.renderer.gallery = function renderGallery(v) {
	return v.map(function(item) { return item.title; }).join(', ');
};


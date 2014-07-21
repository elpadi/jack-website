define(['lib/fn/curry'], function(curry) {
	
	function MagazineManager($container, Magazine) {
		this.$container = $container;
		this._loadedIndexes = [];
		this._magazine = new Magazine($container);
		this._objectCache = {};
		this._objects = {
			prev: null,
			current: {
				name: 'cover',
				textures: {},
				isShowingFront: true,
				isOpen: false
			},
			next: {
				name: 'poster-1',
				textures: {},
				isShowingFront: true,
				isOpen: false
			}
		};
		this.load();
	}

	MagazineManager.prototype.isFirstLoad = true;

	MagazineManager.prototype.trigger = function(name) {
		this.$container.trigger('magazine.' + name);
		return this;
	};

	MagazineManager.prototype.onGroupLoad = function() {
		if (this.isFirstLoad) {
			this._magazine.updateTexture(this._objects.current).resetAnimation('switchsection');
		}
		else {
		}
	};

	MagazineManager.prototype.onLoad = function(objectInfo) {
		var side = ('isFrontLoaded' in objectInfo) ? 'back' : 'front';
		objectInfo.isFrontLoaded = true;
		if (side === 'back') {
			this._objectCache[objectInfo.name] = objectInfo.textures;
		}
		this.load();
	};

	MagazineManager.prototype.loadObject = function(objectInfo) {
		if (!objectInfo) return false;
		var side = ('isFrontLoaded' in objectInfo) ? 'back' : 'front';
		if (!(objectInfo.name in this._objectCache)) {
			objectInfo.textures[side] = THREE.ImageUtils.loadTexture(this.$container.find('#' + objectInfo.name).data('src-' + side), null, curry(this.onLoad, objectInfo).bind(this));
			return true;
		}
		objectInfo.textures = this._objectCache[objectInfo.name];
		return false;
	};

	MagazineManager.prototype.load = function() {
		return this.loadObject(this._objects.current)
			|| this.loadObject(this._objects.next)
			|| this.loadObject(this._objects.prev)
			|| this.onGroupLoad();
	};

	MagazineManager.prototype.flip = function() {
			this._magazine.resetAnimation('flip');
	};

	MagazineManager.prototype.openclose = function() {
			this._magazine.resetAnimation('openclose');
	};

	return MagazineManager;
});


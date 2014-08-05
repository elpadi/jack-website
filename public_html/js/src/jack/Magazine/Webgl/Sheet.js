define(['underscore','threejs','lib/fn/curry','lib/Animation'], function(underscore, three, curry, Animation) {
	
	var FLIP_MATRIX = new THREE.Matrix4().makeRotationY(Math.PI);

	function Sheet($sheet, number) {
		this.onEnterFrame = function() {};
		this.whole = new THREE.Object3D();
		this.loader = new THREE.TextureLoader();
		this.animation = new Animation(0, 1);
		
		this.$container = $sheet;
		this.index = number - 1;
		this.name = 'sheet-' + number;

		var addToScene = this.whole.add.bind(this.whole);
		var createImagePromise = function(pos, pIndex, posGroup, face) {
			return this.createImageMesh(pos, face, pIndex).done(posGroup.add.bind(posGroup));
		}.bind(this);
		var groupPromises = _.map(this.imagePositions, function(pos, pIndex) {
			var promise = $.Deferred(),
					posGroup = new THREE.Object3D();
			posGroup.name = pos;
			// when all image meshes in a group are created, resolve the group.
			$.when.apply(null, _.map(this.faces, curry(createImagePromise, pos, pIndex, posGroup)))
			  .done(function() {
					posGroup.translateX(this.groupTranslations.x[pIndex] * this.width);
					posGroup.translateZ(this.translations.z[pIndex]);
					posGroup.rotation.x = this.rotations.x[pIndex];
					posGroup.rotation.y = this.rotations.y[pIndex];
					promise.resolve(posGroup)
			  }.bind(this));
			return promise;
		}.bind(this));
		// when all the groups are resolved, add to the scene.
		$.when.apply(null, groupPromises).done(function() {
			_.each(arguments, addToScene);
			$sheet.trigger('sectionready');
		});
	
		// push the whole thing up a little bit.
		this.whole.translateY(0.5);
		$sheet.data('magazine-section', this);
	}

	Sheet.prototype.width = 5;
	Sheet.prototype.height = 6.667;
	Sheet.prototype.animationDuration = 500;
	Sheet.prototype.currentOpacity = 0;
	Sheet.prototype.planesLoaded = 0;
	Sheet.prototype.isShowingFront = true;
	Sheet.prototype.isOpen = false;
	Sheet.prototype.name = 'sheet-1';
	Sheet.prototype.index = 0;
	Sheet.prototype.imagePositions = ['left','right'];
	Sheet.prototype.faces = ['front','back'];
	Sheet.prototype.groupTranslations = {
		x: [0,0]
	};
	Sheet.prototype.translations = {
		x: [-0.5,0.5],
		y: [1,1].map(function(n) { return n * 0.125; }),
		z: [0,1].map(function(n) { return n * 0.01; })
	};
	Sheet.prototype.rotations = {
		x: [0,0],
		y: [0, Math.PI]
	};

	Sheet.prototype.createImageMesh = function(pos, face, posIndex) {
		var promise = $.Deferred(),
				plane = new THREE.PlaneGeometry(this.width, this.height);
		(face === 'back') && plane.applyMatrix(FLIP_MATRIX);
		this.loader.load(this.$container.find('.' + pos + '.' + face).attr('src'), function(texture) {
			var mesh = new THREE.Mesh(plane, new THREE.MeshBasicMaterial({
				map: texture,
				transparent: true,
				overdraw: 1,
				opacity: 0
			}));
			mesh.translateX(this.translations.x[posIndex] * this.width);
			mesh.translateY(this.translations.y[posIndex] * this.height);
			(face === 'back') && mesh.translateZ(0.02);
			mesh.visible = false;
			promise.resolve(mesh);
		}.bind(this));
		return promise;
	};

	Sheet.prototype.getObject3D = function() {
		return this.whole;
	};

	Sheet.prototype.getSectionIndex = function() {
		return this.index;
	};

	Sheet.prototype.toggleViewState = function() {
		return this.isOpen ? this.close() : this.open();
	};

	Sheet.prototype.triggerMagEvent = function(name) {
		this.$container.closest('.magazine').trigger('magazine.' + name);
		return this;
	};

	Sheet.prototype.setOpacity = function(opacity) {
		console.log('Sheet.setOpacity --- opacity:', opacity);
		this.whole.traverse(function(obj) {
			if ('material' in obj) {
				console.log('Sheet.setOpacity --- setting opacity to a descendant.');
				obj.material.opacity = opacity;
			}
			else {
				console.log('Sheet.setOpacity --- descendant does not have a material.');
			}
		});
		this.currentOpacity = opacity;
	};

	Sheet.prototype.rotateY = function(angle) {
		this.whole.rotation.y = angle;
	};

	Sheet.prototype.rotateRightY = function(angle) {
		this.whole.getObjectByName('right').rotation.y = angle;
	};

	Sheet.prototype.rotateLeftY = function(angle) {
		this.whole.getObjectByName('left').rotation.y = angle;
	};

	Sheet.prototype.resetAnimation = function(initialValue, finalValue, valueSetter, endEvent) {
		this.animation.stop();
		this.animation = new Animation(initialValue, finalValue, this.animationDuration, function(val) {
			valueSetter(val);
			this.$container.trigger('webglrefresh');
		}.bind(this), curry(this.triggerMagEvent, endEvent).bind(this));
		this.animation.start();
	};

	Sheet.prototype.show = function() {
		console.log('Sheet.show --- ');
		this.whole.traverse(function(obj) { obj.visible = true; });
		this.resetAnimation(this.currentOpacity, 1, this.setOpacity.bind(this), 'sectionshow');
	};

	Sheet.prototype.hide = function() {
		this.resetAnimation(this.currentOpacity, 0, this.setOpacity.bind(this), 'sectionhide');
	};

	Sheet.prototype.flip = function(immediate) {
		this.isShowingFront = !this.isShowingFront;
		if (immediate) {
			this.whole.rotation.y = (Math.floor(this.whole.rotation.y / Math.PI) + 1) * Math.PI;
		}
		else {
			this.resetAnimation(this.whole.rotation.y, (Math.floor(this.whole.rotation.y / Math.PI) + 1) * Math.PI, this.rotateY.bind(this), 'sectionflip');
		}
	};

	Sheet.prototype.open = function() {
		this.isOpen = true;
		this.resetAnimation(this.whole.getObjectByName('right').rotation.y, 0, this.rotateRightY.bind(this), 'sectionopen');
	};

	Sheet.prototype.close = function() {
		this.isOpen = false;
		this.resetAnimation(this.whole.getObjectByName('right').rotation.y, this.rotations.y[1], this.rotateRightY.bind(this), 'sectionclosed');
	};
	
	return Sheet;
});


define(['underscore','threejs','lib/fn/curry','lib/Animation'], function(underscore, three, curry, Animation) {
	
	var FLIP_MATRIX = new THREE.Matrix4().makeRotationY(Math.PI);

	function Sheet($sheet, number) {
		this.onEnterFrame = function() {};
		this.whole = new THREE.Object3D();
		this.animation = new Animation(0, 1);
		
		this.$container = $sheet;
		this.index = number - 1;
		this.name = 'sheet-' + number;
		var parts = _.map(this.parts, this.createPart.bind(this));

		_.each(parts, function(part) {
			this.whole.add(part);
		}.bind(this));
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
	Sheet.prototype.parts = ['left','right'];
	Sheet.prototype.faces = ['front','back'];
	Sheet.prototype.translations = {
		x: [-0.5,0.5],
		z: [0,0.1]
	};
	Sheet.prototype.rotations = [0, Math.PI];

	Sheet.prototype.createPart = function(part, index) {
		var group = new THREE.Object3D();
		group.name = part;
		_.each(_.map(this.faces, curry(this.createPlane.bind(this), part)), function(plane) {
			plane.translateX(this.translations.x[index] * this.width);
			plane.translateZ(this.translations.z[index] * 0.25);
			plane.visible = false;
			group.add(plane);
		}.bind(this));
		group.rotation.y = this.rotations[index];
		return group;
	};

	Sheet.prototype.onPlaneLoaded = function() {
		this.planesLoaded++;
		console.log('Sheet.onPlaneLoaded --- planesLoaded:', this.planesLoaded);
		(this.planesLoaded === this.parts.length * this.faces.length) && this.$container.trigger('sectionready');
	};

	Sheet.prototype.createPlane = function(part, face) {
		var plane = new THREE.PlaneGeometry(this.width, this.height);
		var image = new THREE.MeshBasicMaterial({
			map: THREE.ImageUtils.loadTexture(this.$container.find('.' + part + '.' + face).attr('src'), {}, this.onPlaneLoaded.bind(this)),
			transparent: true,
			opacity: 0
		});
		(face === 'back') && plane.applyMatrix(FLIP_MATRIX);
		return new THREE.Mesh(plane, image);
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
		this.resetAnimation(this.whole.getObjectByName('right').rotation.y, this.rotations[1], this.rotateRightY.bind(this), 'sectionclosed');
	};
	
	return Sheet;
});

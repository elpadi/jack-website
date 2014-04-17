define(['underscore','threejs','lib/fn/curry'], function(underscore, three, curry) {
	
	var FLIP_MATRIX = new THREE.Matrix4().makeRotationY(Math.PI);

	function Sheet($sheet, number) {
		this.onEnterFrame = function() {};
		this.whole = new THREE.Object3D();
		
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
	Sheet.prototype.height = 5;
	Sheet.prototype.isOpen = false;
	Sheet.prototype.isShowingFront = false;
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
			plane.translateZ(this.translations.z[index]);
			plane.visible = false;
			group.add(plane);
		}.bind(this));
		group.rotation.y = this.rotations[index];
		return group;
	};

	Sheet.prototype.createPlane = function(part, face) {
		var plane = new THREE.PlaneGeometry(this.width, this.height);
		var image = new THREE.MeshBasicMaterial({
			map: THREE.ImageUtils.loadTexture(this.$container.find('.' + part + '.' + face).attr('src')),
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

	Sheet.prototype.onOpenEnterFrame = function() {
		var right = this.whole.getObjectByName('right');
		if (right.rotation.y > 0) {
			right.rotation.y -= 0.05;
		}
	};

	Sheet.prototype.onCloseEnterFrame = function() {
		var right = this.whole.getObjectByName('right');
		if (right.rotation.y < this.rotations[1]) {
			right.rotation.y += 0.05;
		}
	};

	Sheet.prototype.open = function() {
		if (!this.isOpen) {
			this.isOpen = true;
			this.$container.closest('.magazine').trigger('magazine.open');
			this.onEnterFrame = this.onOpenEnterFrame.bind(this);
		}
		return this;
	};

	Sheet.prototype.close = function() {
		if (this.isOpen) {
			this.isOpen = false;
			this.$container.closest('.magazine').trigger('magazine.close');
			this.onEnterFrame = this.onCloseEnterFrame.bind(this);
		}
		return this;
	};

	Sheet.prototype.triggerMagEvent = function(name) {
		this.$container.closest('.magazine').trigger('magazine.' + name);
		return this;
	};

	Sheet.prototype.flip = function() {
		this.triggerMagEvent('flip');
		var nextStop = Math.floor(this.whole.rotation.y / Math.PI) + 1;
		this.onEnterFrame = function() {
			if (this.whole.rotation.y < nextStop * Math.PI) {
				this.whole.rotation.y += 0.05;
			}
		}.bind(this);
		return this;
	};

	Sheet.prototype.hide = function() {
		this.onEnterFrame = function() {
			var that = this;
			this.whole.traverse(function(obj) {
				if ('material' in obj) {
					if (obj.material.opacity > 0) {
						obj.material.opacity -= 0.015;
					}
					else {
						obj.visible = false;
						that.triggerMagEvent('sectionhide');
						that.onEnterFrame = function() {};
					}
				}
			});
		}.bind(this);
	};

	Sheet.prototype.show = function() {
		this.onEnterFrame = function() {
			var that = this;
			this.whole.traverse(function(obj) {
				if ('material' in obj) {
					obj.visible = true;
					if (obj.material.opacity < 1) {
						obj.material.opacity += 0.015;
					}
					else {
						that.triggerMagEvent('sectionshow');
						that.onEnterFrame = function() {};
					}
				}
			});
		}.bind(this);
	};

	return Sheet;
});


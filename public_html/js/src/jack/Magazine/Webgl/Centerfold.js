define(['lib/fn/curry','./Sheet'], function(curry, Sheet) {
	
	function Centerfold($centerfold) {
		Sheet.call(this, $centerfold, 0);
		this.name = 'centerfold';
		this.whole.rotation.z = Math.PI * (1 / 2);
	}

	function A() {}
	A.prototype = Sheet.prototype;
	Centerfold.prototype = new A();
	Centerfold.prototype.constructor = Centerfold;

	Centerfold.prototype.parts = ['topleft','topright','bottomleft','bottomright'];
	Centerfold.prototype.faces = ['front','back'];
	Centerfold.prototype.translations = {
		x: [-0.5,0.5,-0.5,0.5],
		y: [0.5,0.5,-0.5,-0.5],
		z: [-2,2,1,-1].map(function(n) { return n * 0.01; })
	};

	Centerfold.prototype.rotations = {
		y: [-Math.PI, 0, -Math.PI, 0],
		x: [0, 0, Math.PI, Math.PI],
	};

	Centerfold.prototype.getSectionIndex = function() {
		return this.name;
	};

	Centerfold.prototype.createPart = function(part, index) {
		var group = new THREE.Object3D();
		group.name = part;
		_.each(_.map(this.faces, curry(this.createPlane.bind(this), part)), function(plane) {
			plane.translateX(this.translations.x[index] * this.width);
			plane.translateY(this.translations.y[index] * this.height);
			plane.translateZ(this.translations.z[index]);
			plane.visible = false;
			group.add(plane);
		}.bind(this));
		group.rotation.x = this.rotations.x[index];
		group.rotation.y = this.rotations.y[index];
		return group;
	};

	Centerfold.prototype.rotateRightY = function(angle) {
		this.whole.getObjectByName('bottomright').rotation.y = angle;
		this.whole.getObjectByName('topright').rotation.y = angle * -1;
	};

	Centerfold.prototype.rotateTopX = function(angle) {
		this.whole.getObjectByName('topright').rotation.x = angle;
		this.whole.getObjectByName('topleft').rotation.x = angle;
	};

	Centerfold.prototype.rotateLeftX = function(angle) {
		this.whole.getObjectByName('bottomright').rotation.x = angle;
		this.whole.getObjectByName('bottomleft').rotation.x = angle;
	};

	Centerfold.prototype.rotateTopY = function(angle) {
		this.whole.getObjectByName('topleft').rotation.y = angle;
		this.whole.getObjectByName('bottomleft').rotation.y = angle;
	};

	Centerfold.prototype.open = function() {
		this.isOpen = true;
		this.resetAnimation(this.whole.getObjectByName('bottomright').rotation.x, 0, this.rotateLeftX.bind(this), 'halfopen');
		this.$container.closest('.magazine').one('magazine.halfopen', function() {
			this.resetAnimation(this.whole.getObjectByName('topleft').rotation.y, 0, this.rotateTopY.bind(this), 'sectionopen');
		}.bind(this));
	};

	Centerfold.prototype.close = function() {
		this.isOpen = false;
		this.resetAnimation(this.whole.getObjectByName('topleft').rotation.y, this.rotations.y[0], this.rotateTopY.bind(this), 'halfclosed');
		this.$container.closest('.magazine').one('magazine.halfclosed', function() {
			this.resetAnimation(this.whole.getObjectByName('bottomright').rotation.x, this.rotations.x[3], this.rotateLeftX.bind(this), 'sectionclosed');
		}.bind(this));
	};

	return Centerfold;
});


define(['lib/fn/curry','./Sheet'], function(curry, Sheet) {
	
	function Centerfold($centerfold) {
		Sheet.call(this, $centerfold, 0);
		this.name = 'centerfold';
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
		z: [0.2,-0.1,0,0.1]
	};
	Centerfold.prototype.rotations = {
		y: [0, -Math.PI, 0, Math.PI],
		x: [-Math.PI, -Math.PI, 0, 0],
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
			plane.translateZ(this.translations.z[index] * 0.25);
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

	Centerfold.prototype.open = function() {
		this.isOpen = true;
		this.resetAnimation(this.whole.getObjectByName('bottomright').rotation.y, 0, this.rotateRightY.bind(this), 'halfopen');
		this.$container.closest('.magazine').one('magazine.halfopen', function() {
			this.resetAnimation(this.whole.getObjectByName('topright').rotation.x, 0, this.rotateTopX.bind(this), 'sectionopen');
		}.bind(this));
	};

	Centerfold.prototype.close = function() {
		this.isOpen = false;
		this.resetAnimation(this.whole.getObjectByName('topright').rotation.x, this.rotations.x[1], this.rotateTopX.bind(this), 'halfclosed');
		this.$container.closest('.magazine').one('magazine.halfclosed', function() {
			this.resetAnimation(this.whole.getObjectByName('bottomright').rotation.y, this.rotations.y[3], this.rotateRightY.bind(this), 'sectionclosed');
		}.bind(this));
	};

	return Centerfold;
});


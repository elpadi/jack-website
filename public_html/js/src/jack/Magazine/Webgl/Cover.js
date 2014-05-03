define(['lib/fn/curry','./Sheet'], function(curry, Sheet) {
	
	function Cover($cover) {
		Sheet.call(this, $cover, 0);
		this.name = 'cover';
	}

	function A() {}
	A.prototype = Sheet.prototype;
	Cover.prototype = new A();
	Cover.prototype.constructor = Cover;

	Cover.prototype.parts = ['left','middle','right'];
	Cover.prototype.faces = ['front','back'];
	Cover.prototype.translations = {
		x: [-1,0,1],
		z: [0.1,0,0.2]
	};
	Cover.prototype.rotations = [-Math.PI, 0, Math.PI];

	Cover.prototype.createPart = function(part, index) {
		var group = new THREE.Object3D();
		group.name = part;
		_.each(_.map(this.faces, curry(this.createPlane.bind(this), part)), function(plane) {
			plane.translateX(this.translations.x[index] * this.width / 2);
			plane.translateZ(this.translations.z[index] * 0.25);
			plane.visible = false;
			group.add(plane);
		}.bind(this));
		group.translateX(this.translations.x[index] * this.width / 2);
		group.rotation.y = this.rotations[index];
		return group;
	};

	Cover.prototype.getSectionIndex = function() {
		return this.name;
	};

	Cover.prototype.open = function() {
		this.isOpen = true;
		this.resetAnimation(this.whole.getObjectByName('right').rotation.y, 0, this.rotateRightY.bind(this), 'halfopen');
		this.$container.closest('.magazine').one('magazine.halfopen', function() {
			this.resetAnimation(this.whole.getObjectByName('left').rotation.y, 0, this.rotateLeftY.bind(this), 'sectionopen');
		}.bind(this));
	};

	Cover.prototype.close = function() {
		this.isOpen = false;
		this.resetAnimation(this.whole.getObjectByName('left').rotation.y, this.rotations[0], this.rotateLeftY.bind(this), 'halfclosed');
		this.$container.closest('.magazine').one('magazine.halfclosed', function() {
			this.resetAnimation(this.whole.getObjectByName('right').rotation.y, this.rotations[2], this.rotateRightY.bind(this), 'sectionclosed');
		}.bind(this));
	};

	return Cover;
});

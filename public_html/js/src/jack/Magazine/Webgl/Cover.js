define(['./Sheet'], function(Sheet) {
	
	function Cover($cover) {
		Sheet.call(this, $cover, 0);
		this.name = 'cover';
	}

	function A() {}
	A.prototype = Sheet.prototype;
	Cover.prototype = new A();
	Cover.prototype.constructor = Cover;

	Cover.prototype.imagePositions = ['left','middle','right'];
	Cover.prototype.faces = ['front','back'];

	Cover.prototype.groupTranslations = {
		x: [-0.5,0,0.5]
	};
	Cover.prototype.translations = {
		x: [-0.5,0,0.5],
		y: [1,1,1].map(function(n) { return n * 0.125; }),
		z: [2,0,1].map(function(n) { return n * 0.01; })
	};
	Cover.prototype.rotations = {
		x: [0,0,0],
		y: [-Math.PI, 0, Math.PI]
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
		this.resetAnimation(this.whole.getObjectByName('left').rotation.y, this.rotations.y[0], this.rotateLeftY.bind(this), 'halfclosed');
		this.$container.closest('.magazine').one('magazine.halfclosed', function() {
			this.resetAnimation(this.whole.getObjectByName('right').rotation.y, this.rotations.y[2], this.rotateRightY.bind(this), 'sectionclosed');
		}.bind(this));
	};

	return Cover;
});

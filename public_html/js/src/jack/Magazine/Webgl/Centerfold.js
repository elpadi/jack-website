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
			plane.translateZ(this.translations.z[index]);
			plane.visible = false;
			group.add(plane);
		}.bind(this));
		group.rotation.x = this.rotations.x[index];
		group.rotation.y = this.rotations.y[index];
		return group;
	};

	Centerfold.prototype.onOpenEnterFrame = function() {
		var bottomright = this.whole.getObjectByName('bottomright');
		var topright = this.whole.getObjectByName('topright');
		var topleft = this.whole.getObjectByName('topleft');
	
		if (bottomright.rotation.y > 0) {
			bottomright.rotation.y -= 0.05;
			topright.rotation.y += 0.05;
		}
		else if (topright.rotation.x < 0) {
			topright.rotation.x += 0.05;
			topleft.rotation.x += 0.05;
		}
	};

	Centerfold.prototype.onCloseEnterFrame = function() {
		var bottomright = this.whole.getObjectByName('bottomright');
		var topright = this.whole.getObjectByName('topright');
		var topleft = this.whole.getObjectByName('topleft');
		
		if (topright.rotation.x > this.rotations.x[1]) {
			topright.rotation.x -= 0.05;
			topleft.rotation.x -= 0.05;
		}
		else if (bottomright.rotation.y < this.rotations.y[3]) {
			bottomright.rotation.y += 0.05;
			topright.rotation.y -= 0.05;
		}
	};

	return Centerfold;
});


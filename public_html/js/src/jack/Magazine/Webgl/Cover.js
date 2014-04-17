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
		z: [0.2,0,0.1]
	};
	Cover.prototype.rotations = [-Math.PI, 0, Math.PI];

	Cover.prototype.createPart = function(part, index) {
		var group = new THREE.Object3D();
		group.name = part;
		_.each(_.map(this.faces, curry(this.createPlane.bind(this), part)), function(plane) {
			plane.translateX(this.translations.x[index] * this.width / 2);
			plane.translateZ(this.translations.z[index]);
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

	Cover.prototype.onOpenEnterFrame = function() {
		var left = this.whole.getObjectByName('left');
		var right = this.whole.getObjectByName('right');
		if (left.rotation.y < 0) {
			left.rotation.y += 0.05;
		}
		else if (right.rotation.y > 0) {
			right.rotation.y -= 0.05;
		}
	};

	Cover.prototype.onCloseEnterFrame = function() {
		var left = this.whole.getObjectByName('left');
		var right = this.whole.getObjectByName('right');
		if (right.rotation.y < this.rotations[2]) {
			right.rotation.y += 0.05;
		}
		else if (left.rotation.y > this.rotations[0]) {
			left.rotation.y -= 0.05;
		}
	};

	return Cover;
});

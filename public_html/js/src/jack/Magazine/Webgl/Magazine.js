define(['jquery','threejs','lib/fn/curry','site/Magazine','./Sheet','./Cover','./Centerfold'], function(jquery, three, curry, Magazine, Sheet, Cover, Centerfold) {
	
	function WebglMagazine($container) {
		Magazine.call(this, $container);
		this.scene = new THREE.Scene();
		this.camera = new THREE.PerspectiveCamera(65, this.width / this.height, 0.1, 2000);
		this.createRenderer();
		this.settings = {
			rotAngles: [0,0],
			rotDistances: [0,0],
			crossfade: 0,
			openclose: { stack: [], phase: 0 },
			sides: [true, true],
			front: null,
			back: null,
			newFront: null,
			newBack: null
		};
		this._animations = {
			current: ''
		};

		this.camera.position.z = 999;
		$container.find('.magazine__sections').append(this.renderer.domElement);
		
		$container.on('webglrefresh', this.render.bind(this));
		this.addRenderObjects();
	}

	function A() {}
	A.prototype = Magazine.prototype;
	WebglMagazine.prototype = new A();
	WebglMagazine.prototype.constructor = WebglMagazine;

	WebglMagazine.prototype.createRenderer = function() {};
	WebglMagazine.prototype.addRenderObjects = function() {};

	WebglMagazine.prototype.render = function() {
		this.renderer.render(this.scene, this.camera);
	};

	WebglMagazine.prototype.resize = function(w, h) {
		this.renderer.setSize(w, h);
		this.camera.aspect = w / h;
		this.camera.updateProjectionMatrix();
		this.renderer.domElement.style.paddingTop = ((window.innerHeight - h) / 2) + 'px';
	};

	return WebglMagazine;
});

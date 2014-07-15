define(['jquery','threejs','lib/fn/curry','site/Magazine','./Sheet','./Cover','./Centerfold'], function(jquery, three, curry, Magazine, Sheet, Cover, Centerfold) {
	
	function WebglMagazine($container) {
		Magazine.call(this, $container);
		this.scene = new THREE.Scene();
		this.camera = new THREE.PerspectiveCamera(55, this.width / this.height, 0.1, 1000);
		this.renderer = new THREE.WebGLRenderer({ alpha: true });

		this.camera.position.z = 15;
		this.renderer.setSize(this.width, this.height)
		$container.find('.magazine__sections').append(this.renderer.domElement);
		
		$container.on('webglrefresh', this.render.bind(this));

		this.cover = new Cover($('#cover'));
		$('#cover').on('sectionready', this.cover.show.bind(this.cover));
		this.scene.add(this.cover.getObject3D());
		
		this.sheets = $container.find('.magazine-poster').map(function(i, el) {
			var sheet = new Sheet($(el), i + 1);
			this.scene.add(sheet.getObject3D());
			return sheet
		}.bind(this)).get();
		
		this.centerfold = new Centerfold($('#centerfold'));
		this.scene.add(this.centerfold.getObject3D());
	}

	function A() {}
	A.prototype = Magazine.prototype;
	WebglMagazine.prototype = new A();
	WebglMagazine.prototype.constructor = WebglMagazine;

	WebglMagazine.prototype.render = function() {
		this.renderer.render(this.scene, this.camera);
	};

	return WebglMagazine;
});

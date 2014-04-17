define(['jquery','threejs','site/Magazine','./Sheet','./Cover','./Centerfold'], function(jquery, three, Magazine, Sheet, Cover, Centerfold) {
	
	function WebglMagazine($container) {
		Magazine.call(this, $container);
		this.scene = new THREE.Scene();
		this.camera = new THREE.PerspectiveCamera(75, this.width / this.height, 0.1, 1000);
		this.renderer = new THREE.WebGLRenderer();

		this.camera.position.z = 15;
		this.renderer.setClearColor( 0xffffff, 1);
		this.renderer.setSize(this.width, this.height)
		$container.find('.magazine__sections').append(this.renderer.domElement);
		
		this.cover = new Cover($('#cover'));
		this.scene.add(this.cover.getObject3D());
		this.sheets = $container.find('.magazine-poster').map(function(i, el) {
			var sheet = new Sheet($(el), i + 1);
			this.scene.add(sheet.getObject3D());
			return sheet
		}.bind(this)).get();
		this.centerfold = new Centerfold($('#centerfold'));
		this.scene.add(this.centerfold.getObject3D());
		
		var render = function() {
			requestAnimationFrame(render);
			this.getCurrentSection().onEnterFrame();
			this.renderer.render(this.scene, this.camera);
		}.bind(this);
		render();
		this.cover.show();
	}

	function A() {}
	A.prototype = Magazine.prototype;
	WebglMagazine.prototype = new A();
	WebglMagazine.prototype.constructor = WebglMagazine;

	return WebglMagazine;
});

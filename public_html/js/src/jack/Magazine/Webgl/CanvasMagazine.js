define(['site/Magazine/Webgl/Magazine','./Sheet','./Cover','./Centerfold'], function(WebglMagazine, Sheet, Cover, Centerfold) {
	
	function CanvasMagazine($container) {
		WebglMagazine.call(this, $container);
	}

	function A() {}
	A.prototype = WebglMagazine.prototype;
	CanvasMagazine.prototype = new A();
	CanvasMagazine.prototype.constructor = CanvasMagazine;

	CanvasMagazine.prototype.createRenderer = function() {
		this.renderer = new THREE.CanvasRenderer({ alpha: true });
	};

	CanvasMagazine.prototype.addRenderObjects = function() {
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
	};

	return CanvasMagazine;
});

define(['site/Magazine/Webgl/Magazine',window.CONFIG.SITE.PATH_PREFIX+'add-shader?s=magazine'], function(WebglMagazine, MagazineShader) {

	function linear(t, b, c, d) {
		return b + c * t / d;
	}
	
	function ShaderMagazine($container) {
		WebglMagazine.call(this, $container);
	}

	function A() {}
	A.prototype = WebglMagazine.prototype;
	ShaderMagazine.prototype = new A();
	ShaderMagazine.prototype.constructor = WebglMagazine;

	ShaderMagazine.prototype.createRenderer = function() {
		this.renderer = new THREE.WebGLRenderer({ alpha: true });
	};

	ShaderMagazine.prototype.addRenderObjects = function() {
		this.geometry = new THREE.PlaneGeometry(
			window.CONFIG.MAGAZINE.WEBGL_WIDTH * 3 * window.CONFIG.MAGAZINE.WEBGL_UNIT_MULTIPLIER,
			window.CONFIG.MAGAZINE.WEBGL_HEIGHT * 2 * window.CONFIG.MAGAZINE.WEBGL_UNIT_MULTIPLIER,
			6,
			2
		);
		this.material = new THREE.ShaderMaterial(MagazineShader);
		this.material.transparent = true;
		this.material.side = THREE.DoubleSide;
		this.mesh = new THREE.Mesh(this.geometry, this.material);
		this.scene.add(this.mesh);
	};

	ShaderMagazine.prototype.onLoad = function(objects) {
	};

	ShaderMagazine.prototype.updateTexture = function(info) {
		switch (info.name) {
			case 'cover':
				this.settings.sectionType = 0;
				this.settings.rotAngles = [-Math.PI, Math.PI];
				this.settings.rotDistances = [this.geometry.width / this.geometry.widthSegments, this.geometry.width / this.geometry.widthSegments];
				break;
			case 'centerfold':
				this.settings.sectionType = 2;
				this.settings.rotAngles = [Math.PI, -Math.PI];
				this.settings.rotDistances = [0, 0];
				break;
			default:
				this.settings.sectionType = 1;
				this.settings.rotAngles = [-Math.PI, 0];
				this.settings.rotDistances = [0, 0];
		}
		this.settings.newFront = info.textures.front;
		this.settings.newBack = info.textures.back;
		this.settings.crossfade = 0;
		this.settings.flipAngle = info.isShowingFront ? 0 : Math.PI;
		this.settings.sides[1] = info.isShowingFront;
		this.settings.openclose.phase = info.isOpen ? 2 : 0;
		this.settings.openclose.stack = !info.isOpen ? [] : (this.settings.sectionType === 1 ? [0] : [0,1]);
		return this;
	};

	ShaderMagazine.prototype.resetAnimation = function(name) {
		this._animations.current = name;
		this._animations[name] = {
			startTime: Date.now(),
			duration: window.CONFIG.MAGAZINE.ANIMATIONS[name.toUpperCase()].DURATION
		};
		switch (name) {
			case 'switchsection':
				this.mesh.material.uniforms.newFront.value = this.settings.newFront;
				this.mesh.material.uniforms.newBack.value = this.settings.newBack;
				this.mesh.material.uniforms.newSide.value = this.settings.sides[1] ? 0 : 1;
				this.mesh.material.uniforms.crossfade.value = 0;
				this.mesh.material.uniforms.sectionType.value = this.settings.sectionType;
				this.mesh.material.uniforms.rotMat1.value.makeRotationY(this.settings.openclose.phase === 0 ? this.settings.rotAngles[0] : 0);
				this.mesh.material.uniforms.rotMat2.value.makeRotationY(this.settings.openclose.phase === 0 ? this.settings.rotAngles[1] : 0);
				this.mesh.material.uniforms.rotFlip1.value = this.settings.openclose.phase === 0 ? 1 : 0;
				this.mesh.material.uniforms.rotFlip2.value = this.settings.openclose.phase === 0 ? 1 : 0;
				this.mesh.material.uniforms.rotDist1.value = this.settings.rotDistances[0];
				this.mesh.material.uniforms.rotDist2.value = this.settings.rotDistances[1];
				break;
			case 'openclose':
				break;
		}
		this.animate();
	};

	ShaderMagazine.prototype.onEnterFrame = function(elapsed) {
		var animation = this._animations[this._animations.current];
		switch (this._animations.current) {
			case 'switchsection':
				this.mesh.material.uniforms.crossfade.value = linear(elapsed, 0, 1, animation.duration);
				// update uniforms
				break;
			case 'flip':
				this.mesh.material.uniforms.flipRotMat.value.makeRotationY(linear(elapsed, this.settings.flipAngle, Math.PI, animation.duration));
				this.mesh.material.uniforms.side.value = elapsed < animation.duration / 2 ? (this.settings.sides[0] ? 0 : 1) : (this.settings.sides[0] ? 1 : 0);
				break;
			case 'openclose':
				if (this.settings.openclose.phase < 2) {
					this.settings.openclose.stack.push(this.settings.openclose.phase);
					var mat = this.mesh.material.uniforms['rotMat' + (this.settings.openclose.phase + 1)].value;
					var rotFlip = this.mesh.material.uniforms['rotFlip' + (this.settings.openclose.phase + 1)];
					var initial = this.settings.rotAngles[this.settings.openclose.phase];
					var change = -this.settings.rotAngles[this.settings.openclose.phase];
				}
				else {
					var phase = this.settings.openclose.stack.pop();
					var mat = this.mesh.material.uniforms['rotMat' + (phase + 1)].value;
					var rotFlip = this.mesh.material.uniforms['rotFlip' + (phase + 1)];
					var initial = 0;
					var change = this.settings.rotAngles[phase];
				}
				var angle = linear(elapsed, initial, change, animation.duration);
				rotFlip.value = this.settings.openclose.phase < 2 ? (Math.abs(angle) > Math.abs(initial) / 2 ? 1 : 0) : (Math.abs(angle) < Math.abs(change) / 2 ? 0 : 1);
				mat.makeRotationY(angle);
				break;
		}
	};

	ShaderMagazine.prototype.onAnimationEnd = function() {
		var animation = this._animations[this._animations.current];
		switch (this._animations.current) {
			case 'switchsection':
				this.mesh.material.uniforms.front.value = this.settings.newFront;
				this.mesh.material.uniforms.back.value = this.settings.newBack;
				this.mesh.material.uniforms.side.value = this.settings.sides[1] ? 0 : 1;
				this.mesh.material.uniforms.crossfade.value = 0;
				// update uniforms
				break;
			case 'flip':
				this.settings.flipAngle += Math.PI;
				this.mesh.material.uniforms.flipRotMat.value.makeRotationY(this.settings.flipAngle);
				this.settings.sides[0] = !this.settings.sides[0];
				break;
			case 'openclose':
				var mat = this.mesh.material.uniforms['rotMat' + (this.settings.openclose.phase % 2 === 0 ? '1' : '2')].value;
				var initial = this.settings.openclose.phase < 2 ? this.settings.rotAngles[this.settings.openclose.phase] : 0;
				var change = this.settings.openclose.phase < 2 ? -this.settings.rotAngles[this.settings.openclose.phase] : this.settings.rotAngles[this.settings.openclose.phase - 2];
				mat.makeRotationY(initial + change);
				this.settings.openclose.phase = (this.settings.openclose.phase + (this.settings.sectionType === 1 ? 2 : 1)) % 4;
				(this.settings.openclose.phase % 2 !== 0) && this.resetAnimation('openclose');
				break;
		}
	};

	ShaderMagazine.prototype.animate = function() {
		var animation = this._animations[this._animations.current];
		var mag = this;
		(function a() {
			var now = Date.now();
			var elapsed = now - animation.startTime;
			if (elapsed < animation.duration) {
				mag.onEnterFrame(elapsed);
				requestAnimationFrame(a);
			}
			else {
				mag.onAnimationEnd();
			}
			mag.render();
		})();
	};

	return ShaderMagazine;
});

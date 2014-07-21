uniform float crossfade;
uniform int side;
uniform int newSide;
uniform sampler2D front;
uniform sampler2D back;
uniform sampler2D newFront;
uniform sampler2D newBack;

varying vec2 vUv;
varying float vAlpha;
varying float vFlip;
varying float vFlip2;

void main() {
	vec4 new;
	vec4 cur;
	vec4 imgTex;

	if (side == 0 ^^ vFlip < 0.5) {
		cur = texture2D(back, vec2(1.0 - vUv.x, vUv.y));
	}
	else {
		cur = texture2D(front, vUv);
	}
	if (newSide == 0 ^^ vFlip2 < 0.5) {
		new = texture2D(newBack, vec2(1.0 - vUv.x, vUv.y));
	}
	else {
		new = texture2D(newFront, vUv);
	}

	imgTex = mix(cur, new, crossfade);
	imgTex.a = vAlpha;

	gl_FragColor = imgTex;
}


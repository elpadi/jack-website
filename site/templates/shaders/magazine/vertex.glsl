uniform int side;
uniform int sectionType;
uniform int rotFlip1;
uniform int rotFlip2;
uniform float widthSegment;
uniform float rotDist1;
uniform float rotDist2;
uniform mat4 flipRotMat;
uniform mat4 rotMat1;
uniform mat4 rotMat2;

varying vec2 vUv;
varying float vAlpha;
varying float vFlip;
varying float vFlip2;

void main() {
	vec3 p = position;
	vec4 p4;
	vUv = uv;
	vAlpha = 1.0;
	vFlip = 0.0;
	vFlip2 = 0.0;

	if (sectionType == 0) {
		/*
		if (side == 0 && rotFlip1 == 1 && uv.x < 0.5 - widthSegment) {
			vAlpha = 0.0;
		}
		if (side == 0 && rotFlip2 == 1 && uv.x > 0.5 + widthSegment) {
			vAlpha = 0.0;
		}
		if (side == 1 && rotFlip1 == 1 && uv.x < 0.5 - widthSegment) {
			vFlip = 1.0;
		}
		if (side == 1 && rotFlip2 == 1 && uv.x > 0.5 + widthSegment) {
			vFlip = 1.0;
		}
		if (side == 1 && rotFlip1 == 1 && uv.x > 0.5 - widthSegment) {
			vAlpha = 0.0;
		}
		*/
		/*
		else {
			if (uv.x > 0.5 - widthSegment) {
				vAlpha = 0.0;
			}
			vFlip = float(rotFlip1);
		}
		*/
		if (uv.x < 0.5 - widthSegment) {
			p.x += rotDist1;
			p.z += 10.0;
			p4 = rotMat1 * vec4(p, 0.0);
			p = p4.xyz;
			p.x -= rotDist1;
			p.z -= 10.0;
		}
		if (uv.x > 0.5 + widthSegment) {
			p.x -= rotDist2;
			p.z += 20.0;
			p4 = rotMat2 * vec4(p, 0.0);
			p = p4.xyz;
			p.x += rotDist2;
			p.z -= 20.0;
		}
	}
	if (sectionType != 0) {
		p.x *= (2.0 / 3.0);
	}
	if (sectionType != 2) {
		p.y *= 0.5;
	}
	
	gl_Position = projectionMatrix * modelViewMatrix * flipRotMat * vec4(p, 1.0);
}


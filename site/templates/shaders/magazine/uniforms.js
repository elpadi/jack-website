{
	"sectionType": { type:"i", value: 0 }, /* 0 for cover, 1 for poster, 2 for centerfold. */
	"side": { type:"i", value: 0 }, /* 0 for front, 1 for back. */
	"newSide": { type:"i", value: 0 }, /* 0 for front, 1 for back. */
	"rotFlip1": { type:"i", value: 0 }, /* 1 if the first rotation has flipped its face. */
	"rotFlip2": { type:"i", value: 0 }, /* 1 if the second rotation has flipped its face. */
	"crossfade": { type:"f", value: 0 }, /* section switch crossfade [0,1]. */
	"widthSegment": { type:"f", value: 1/6 }, /* 1 / width segement count */
	"heightSegment": { type:"f", value: 1/2 }, /* 1 / height segement count */
	"rotDist1": { type:"f", value: 0 }, /* how far is rotation axis from origin. */
	"rotDist2": { type:"f", value: 0 }, /* how far is rotation axis from origin. */
	"flipRotMat": { type:"m4", value: new THREE.Matrix4() },
	"rotMat1": { type:"m4", value: new THREE.Matrix4() },
	"rotMat2": { type:"m4", value: new THREE.Matrix4() },
	"front": { type:"t", value: null },
	"back": { type:"t", value: null },
	"newFront": { type:"t", value: null },
	"newBack": { type:"t", value: null }
}

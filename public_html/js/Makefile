.PHONY: all

JS_FILES := $(shell find dist -name \*.js -print)
all: $(JS_FILES)

dist/main.min.js: src/main.js dist/rjs.config
	cp dist/rjs.config dist/main.min.js
	groundskeeper < src/main.js | uglifyjs2 >> dist/main.min.js

dist/main.js: src/main.js src/rjs.config
	cp src/rjs.config dist/main.js
	cat src/main.js >> dist/main.js

dist/vendor/jquery.min.js: src/bower_components/jquery/dist/jquery.min.js
	cp src/bower_components/jquery/dist/jquery.min.js dist/vendor/jquery.min.js

dist/vendor/underscore.min.js: src/bower_components/underscore/underscore.js
	groundskeeper < src/bower_components/underscore/underscore.js | uglifyjs2 > dist/vendor/underscore.min.js

dist/vendor/require.min.js: src/bower_components/requirejs/require.js
	groundskeeper < src/bower_components/requirejs/require.js | uglifyjs2 > dist/vendor/require.min.js

dist/lib/SectionSwitcher.min.js: src/bower_components/SectionSwitcher/dist/SectionSwitcher.js
	groundskeeper < src/bower_components/SectionSwitcher/dist/SectionSwitcher.js | uglifyjs2 > dist/lib/SectionSwitcher.min.js

dist/lib/fn/curry.js: src/bower_components/js-libs/fn/curry.js
	groundskeeper < src/bower_components/js-libs/fn/curry.js | uglifyjs2 > dist/lib/fn/curry.js

dist/lib/Animation.js: src/bower_components/js-libs/Animation.js
	groundskeeper < src/bower_components/js-libs/Animation.js | uglifyjs2 > dist/lib/Animation.js

dist/lib/ui/ToggleButton.js: src/bower_components/js-libs/ui/ToggleButton.js
	groundskeeper < src/bower_components/js-libs/ui/ToggleButton.js | uglifyjs2 > dist/lib/ui/ToggleButton.js

dist/lib/dom/absolute-fixed.js: src/bower_components/js-libs/dom/absolute-fixed.js
	groundskeeper < src/bower_components/js-libs/dom/absolute-fixed.js | uglifyjs2 > dist/lib/dom/absolute-fixed.js

dist/lib/dom/window-height.js: src/bower_components/js-libs/dom/window-height.js
	groundskeeper < src/bower_components/js-libs/dom/window-height.js | uglifyjs2 > dist/lib/dom/window-height.js

dist/lib/dom/trackers/scroll.js: src/bower_components/js-libs/dom/trackers/scroll.js
	groundskeeper < src/bower_components/js-libs/dom/trackers/scroll.js | uglifyjs2 > dist/lib/dom/trackers/scroll.js

dist/lib/dom/trackers/resize.js: src/bower_components/js-libs/dom/trackers/resize.js
	groundskeeper < src/bower_components/js-libs/dom/trackers/resize.js | uglifyjs2 > dist/lib/dom/trackers/resize.js

dist/jack/Magazine.js: src/jack/Magazine.js
	groundskeeper < src/jack/Magazine.js | uglifyjs2 > dist/jack/Magazine.js

dist/jack/Magazine/Webgl/Magazine.js: src/jack/Magazine/Webgl/Magazine.js
	groundskeeper < src/jack/Magazine/Webgl/Magazine.js | uglifyjs2 > dist/jack/Magazine/Webgl/Magazine.js

dist/jack/Magazine/Webgl/Cover.js: src/jack/Magazine/Webgl/Cover.js
	groundskeeper < src/jack/Magazine/Webgl/Cover.js | uglifyjs2 > dist/jack/Magazine/Webgl/Cover.js

dist/jack/Magazine/Webgl/Sheet.js: src/jack/Magazine/Webgl/Sheet.js
	groundskeeper < src/jack/Magazine/Webgl/Sheet.js | uglifyjs2 > dist/jack/Magazine/Webgl/Sheet.js

dist/jack/Magazine/Webgl/Centerfold.js: src/jack/Magazine/Webgl/Centerfold.js
	groundskeeper < src/jack/Magazine/Webgl/Centerfold.js | uglifyjs2 > dist/jack/Magazine/Webgl/Centerfold.js


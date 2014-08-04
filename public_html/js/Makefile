
DIRS := $(shell find src/jack -mindepth 1 -type d | grep -v 'test' | sed 's/^src/dist/')
JS_FILES := $(shell find src/jack -type f -name '*.js' | grep -v 'test' | sed 's/^src/dist/')

LIB_DIRS := $(shell find src/bower_components/js-libs -mindepth 1 -type d | grep -v 'test' | sed 's,^src/bower_components/js-libs,dist/lib,')
LIB_JS_FILES := $(shell find src/bower_components/js-libs -type f -name '*.js' | grep -v 'test' | sed 's,^src/bower_components/js-libs,dist/lib,')

.PHONY: all
all: $(DIRS) $(LIB_DIRS) dist/vendor $(JS_FILES) $(LIB_JS_FILES) dist/main.js dist/main.admin.js dist/vendor/jquery.js dist/vendor/underscore.js dist/vendor/require.js dist/vendor/three.js dist/vendor/modernizr.js dist/vendor/jquery-ui.js dist/vendor/jquery.ui.accordion.js dist/vendor/eventemitter2.js dist/vendor/dropzone.js dist/vendor/SectionSwitcher.js dist/rjs.init.js

dist/lib/%.js: src/bower_components/js-libs/%.js
	groundskeeper < $< | uglifyjs > $@

dist/%.js: src/%.js
	groundskeeper < $< | uglifyjs > $@

$(DIRS) $(LIB_DIRS) dist/vendor:
	if [ ! -d $@ ]; then mkdir -p $@; fi

dist/rjs.init.js: src/rjs.init.remote.js
	$(eval $@_FILE := $(shell ls -tR dist | grep '\.js' | head -n 1))
	$(eval $@_MTIME := $(shell [[ "`uname -s`" = "Darwin" ]] && stat -f %m dist/$($@_FILE) || stat -c %Y dist/$($@_FILE)))
	uglifyjs < src/rjs.init.remote.js | sed 's/LAST_MTIME/$($@_MTIME)/' > dist/rjs.init.js

dist/vendor/jquery.js: src/bower_components/jquery/dist/jquery.min.js
	cp src/bower_components/jquery/dist/jquery.min.js dist/vendor/jquery.js

dist/vendor/underscore.js: src/bower_components/underscore/underscore.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/require.js: src/bower_components/requirejs/require.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/modernizr.js: src/bower_components/modernizr/modernizr.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/jquery-ui.js: src/bower_components/jquery-ui/ui/jquery-ui.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/jquery.ui.accordion.js: src/bower_components/jquery-ui/ui/jquery.ui.accordion.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/eventemitter2.js: src/bower_components/eventemitter2/lib/eventemitter2.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/dropzone.js: src/bower_components/dropzone/downloads/dropzone-amd-module.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/SectionSwitcher.js: src/bower_components/SectionSwitcher/dist/SectionSwitcher.js
	groundskeeper < $< | uglifyjs > $@

dist/vendor/three.js: src/vendor/three.min.js
	cp src/vendor/three.min.js dist/vendor/three.js


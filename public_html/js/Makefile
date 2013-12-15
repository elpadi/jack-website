.PHONY: all main admin

all: main admin

main: dist/main.js dist/main.min.js

admin: dist/main.admin.js

dist/main.js: src/main.js build.js src/bower_components
	r.js -o build.js optimize=none paths.requireLib=almond/almond include=requireLib name=../main out=dist/main.js

dist/main.min.js: dist/main.js
	cp dist/main.js dist/main.min.js

dist/main.admin.js: src/main.admin.js build.js src/bower_components
	r.js -o build.js optimize=none paths.requireLib=almond/almond include=requireLib name=../main.admin out=dist/main.admin.js


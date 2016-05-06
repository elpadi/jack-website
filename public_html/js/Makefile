.PHONY: all check

LIB_FILES := $(shell find lib -type f | grep -v 'app')

all: check main.min.js

build/final.js: app.js $(LIB_FILES) lib/app.js
	cat $^ > $@

check:
	@which uglifyjs > /dev/null || ( echo "UglifyJS is missing." && false )

main.min.js: build/final.js
	uglifyjs --mangle --compress drop_console=true $^ --output $@


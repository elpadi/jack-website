DIRS := $(shell find src -mindepth 1 -type d | sed 's/^src/dist/')
DIST_FILES := $(shell find src -type f -name '*.css' | sed 's/^src/dist/')

.PHONY: all
all: $(DIRS) $(DIST_FILES)

dist/%.css: src/%.css
	cleancss $< > $@

$(DIRS):
	if [ ! -d $@ ]; then mkdir -p $@; fi

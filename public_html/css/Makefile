.PHONY: all check

all: check main.min.css

check:
	@which postcss > /dev/null || ( echo "PostCSS is missing." && false )
	@which cssmin > /dev/null || ( echo "CSSMin is missing." && false )

main.min.css: build/final.css
	cssmin $^ > $@

build/final.css: $(shell find base components sections -type f) main.css
	postcss --use postcss-import --use autoprefixer main.css -o $@

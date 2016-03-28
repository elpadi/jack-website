.PHONY: css

all:
	@echo "You must select a target."

css: assets/css/main.css

assets/css/main.build.css: $(shell find public_html/css -type f)
	postcss --use postcss-import --use autoprefixer public_html/css/main.css -o assets/css/main.build.css

assets/css/main.css: assets/css/main.build.css
	cssmin assets/css/main.build.css > assets/css/main.css

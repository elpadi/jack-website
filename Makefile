.PHONY: css

ASSETS_DIR := public_html/admin/assets

all:
	@echo "You must select a target."

css: $(ASSETS_DIR)/css/main.css

$(ASSETS_DIR)/css/main.build.css: $(shell find public_html/css -type f)
	postcss --use postcss-import --use autoprefixer public_html/css/main.css -o $(ASSETS_DIR)/css/main.build.css

$(ASSETS_DIR)/css/main.css: $(ASSETS_DIR)/css/main.build.css
	cssmin $(ASSETS_DIR)/css/main.build.css > $(ASSETS_DIR)/css/main.css

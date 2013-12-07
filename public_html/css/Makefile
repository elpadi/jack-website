all: dist/admin.css dist/main.css

dist/admin.css: src/admin.less src/lib
	lessc src/admin.less > dist/admin.css

dist/main.css: src/main.less src/pages src/lib src/components
	lessc src/main.less > dist/main.css

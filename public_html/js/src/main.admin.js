require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

require(['jquery','dropzone/downloads/dropzone-amd-module'], function(jquery, Dropzone) {
	$('.admin-form-image').each(function(i, el) {
		new Dropzone(el, {
			paramName: el.getAttribute('data-file-input-name'),
			uploadMultiple: false
		});
	});
});

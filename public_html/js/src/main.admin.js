require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

function getIssueImageFromInputName(issue, name) {
	switch (name) {
		case 'FrontCover':
			return issue.covers.front;
	}
	return '';
}

require(['jquery','dropzone/downloads/dropzone-amd-module'], function(jquery, Dropzone) {
	$('.admin-form-image').each(function(i, el) {
		var dropzone = new Dropzone(el, {
			paramName: el.getAttribute('data-file-input-name'),
			uploadMultiple: false,
			acceptedFiles: "image/jpeg",
		});
		dropzone.on('success', function(file, response) {
			if (response.success) {
				var src = getIssueImageFromInputName(response.issue, el.getAttribute('data-file-input-name'));
				console.log('Issue image uploaded.', response);
				$(el).closest('.issue-image').find('.issue-image__image img').attr('src', src);
			}
			else {
				alert("Error: " + response.error);
			}
		});
	});
});

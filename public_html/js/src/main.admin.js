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
		case 'BackCover':
			return issue.covers.back;
		case 'Index':
			return issue.covers.index;
		case 'CoverPoster':
			return issue.covers.poster;
		case 'FrontCenterfold':
			return issue.centerfold.front;
		case 'BackCenterfold':
			return issue.centerfold.back;
	}
	return '';
}

require(['jquery','lib/ui/SectionSwitcher','lib/ui/SectionSwitcher/SectionLinks'], function(jquery, SectionSwitcher, SectionLinks) {
	$('.tabs').each(function(i, el) {
		var tabs = new SectionSwitcher($(el));
		tabs.addComponent(SectionLinks);
		tabs.init();
	});
});

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

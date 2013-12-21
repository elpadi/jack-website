define(['jquery','lib/ui/SectionSwitcher/SectionLinks'], function(jquery, SectionLinks) {

	var MagSectionLinks = {
		init: function($container) {
			this.$lis = $container.find('.section-switcher__links li');
			SectionLinks.init.call(this, $container);
		},
		sectionlinkclicked: function(e) {
			this.switchByHash(e.currentTarget.hash, e.currentTarget.getAttribute('data-section-face') === 'back');
		},
		sectionselected: function(newIndex, oldIndex, flipped) {
			this.$lis.removeClass('selected selected--front selected--back')
				.eq(newIndex).addClass('selected ' + 'selected--' + (flipped ? 'back' : 'front'));
		}
	};

	return $.extend({}, SectionLinks, MagSectionLinks);

});

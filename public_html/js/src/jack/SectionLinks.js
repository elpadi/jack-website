define(['jquery','lib/ui/SectionSwitcher/SectionLinks'], function(jquery, SectionLinks) {

	var MagSectionLinks = {
		sectionlinkclicked: function(e) {
			this.switchByHash(e.currentTarget.hash, e.currentTarget.getAttribute('data-section-face') === 'back');
		},
		sectionselected: function(newIndex, oldIndex, flipped) {
			this.$links.removeClass('selected')
				.eq((newIndex * 2) + (flipped ? 1 : 0)).addClass('selected');
		}
	};

	return $.extend({}, SectionLinks, MagSectionLinks);

});

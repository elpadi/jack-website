define(['jquery','lib/ui/SectionSwitcher/ArrowNav'], function(jquery, ArrowNav) {

	var MagArrowNav = {
		arrowclicked: function(e) {
			this.switchByHash(e.currentTarget.hash, $(e.currentTarget).data('flip'));
		},
		sectionswitched: function(newIndex, oldIndex, flipped) {
			var nextIndex = flipped ? this.getNextIndex(newIndex) : newIndex;
			var prevIndex = flipped ? newIndex : this.getPrevIndex(newIndex);
			console.log('MagArrowNav.sectionswitched', 'nextIndex', nextIndex, 'prevIndex', prevIndex);
			this.arrows.$prev
				.attr('href', '#' + this.$elements.eq(prevIndex).attr('id'))
				.data('flip', flipped);
			this.arrows.$next
				.attr('href', '#' + this.$elements.eq(nextIndex).attr('id'))
				.data('flip', !flipped);
			this.$container.toggleClass('section-switcher--left-edge', (prevIndex === false) && !flipped)
				.toggleClass('section-switcher--right-edge', (nextIndex === false) && flipped);
		}
	};

	return $.extend({}, ArrowNav, MagArrowNav);

});

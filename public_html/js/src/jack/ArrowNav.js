define(['jquery','lib/ui/SectionSwitcher/ArrowNav'], function(jquery, ArrowNav) {

	var MagArrowNav = {
		arrowclicked: function(e) {
			this.switchByHash(e.currentTarget.hash, $(e.currentTarget).data('flip'));
		},
		sectionswitched: function(newIndex, oldIndex, flipped) {
			var _this = this;
			var nextIndex = flipped ? this.getNextIndex(newIndex) : newIndex;
			var prevIndex = flipped ? newIndex : this.getPrevIndex(newIndex);
			console.log('MagArrowNav.sectionswitched', 'nextIndex', nextIndex, 'prevIndex', prevIndex);
			setTimeout(function() {
				this.arrows.$prev
					.attr('href', '#' + this.$elements.eq(prevIndex).attr('id'))
					.data('flip', flipped === false);
				this.arrows.$next
					.attr('href', '#' + this.$elements.eq(nextIndex).attr('id'))
					.data('flip', flipped === false);
			}.bind(this), 16);
			this.$container.toggleClass('section-switcher--left-edge', (prevIndex === false) && !flipped)
				.toggleClass('section-switcher--right-edge', (nextIndex === false) && flipped);
		}
	};

	return $.extend({}, ArrowNav, MagArrowNav);

});

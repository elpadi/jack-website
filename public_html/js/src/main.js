require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

require(['jquery','lib/ui/SectionSwitcher','lib/ui/SectionSwitcher/ArrowNav','lib/ui/SectionSwitcher/SectionLinks'], function(jquery, SectionSwitcher, ArrowNav, SectionLinks) {

	/*
	function Magazine($mag) {
		var _this = this;
		this.$mag = $mag;
		this.$posters = $mag.find('.magazine__section');
	}

	Magazine.prototype.currentIndex = 0;
	Magazine.prototype.isFlipped = false;

	Magazine.prototype.toggleOpen = function(index) {
		this.$posters.eq(index).toggleClass('open');
	};

	Magazine.prototype.open = function(index) {
		this.$posters.eq(index).addClass('open');
	};

	Magazine.prototype.close = function(index) {
		this.$posters.eq(index).removeClass('open');
	};

	Magazine.prototype.flip = function(index) {
		this.$posters.eq(index).toggleClass('flip');
		this.isFlipped = true;
	};

	Magazine.prototype.reverse = function(index) {
		var _this = this;
		if (this.$posters.eq(index).data('close-before-flip')) {
			this.close();
			setTimeout(function() { _this.flip(index); }, 2000);
			setTimeout(function() { _this.open(index); }, 3000);
		}
		else {
			this.flip(index);
		}
	};
	*/

	//window.mag = new Magazine($('.magazine'));
	var mag = new SectionSwitcher($('.magazine'));
	mag.addComponent(ArrowNav).addComponent(SectionLinks).init();
	window.mag = mag;
});

require(['lib/dom/absolute-fixed']);
require(['lib/dom/window-height']);

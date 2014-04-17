define(['jquery'], function(jquery) {
	
	function Magazine($container) {
		this.width = $container.find('.magazine__sections').width();
		this.height = $container.find('.magazine__sections').height();
	}

	Magazine.prototype.width = window.innerWidth / 2;
	Magazine.prototype.height = window.innerHeight / 2;
	Magazine.prototype.currentSection = 'cover';

	Magazine.prototype.getCurrentSection = function() {
		return (typeof(this.currentSection) === 'string') ? this[this.currentSection] : this.sheets[this.currentSection];
	};

	Magazine.prototype.getNextSection = function() {
		if (this.currentSection === 'cover') return this.sheets[0];
		if (this.currentSection === 'centerfold') return undefined;
		return (this.currentSection < this.sheets.length - 1) ? this.sheets[this.currentSection + 1] : this.centerfold;
	};

	Magazine.prototype.getPreviousSection = function() {
		if (this.currentSection === 'cover') return undefined;
		if (this.currentSection === 'centerfold') return this.sheets[this.sheets.length - 1];
		return (this.currentSection > 0) ? this.sheets[this.currentSection - 1] : this.cover;
	};

	Magazine.jqueryPlugin = function(Subclass) {
		$.fn.magazine = function() {
			return this.each(function(i, el) {
				var $el = $(el);
				$el.data('magazine', new Subclass($el));
			});
		};
	};

	return Magazine;
});

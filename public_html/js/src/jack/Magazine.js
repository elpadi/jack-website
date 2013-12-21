define(['lib/ui/SectionSwitcher','lib/fn/bind','lib/fn/curry','lib/fn/timedSeq'], function(SectionSwitcher, bind, curry, seq) {

	function Magazine($container) {
		SectionSwitcher.call(this, $container);
		this.$title = $container.find('.magazine__section-title');
	}

	function A() {}
	A.prototype = SectionSwitcher.prototype;
	Magazine.prototype = new A();
	Magazine.prototype.constructor = Magazine;

	Magazine.prototype.isFlipped = false;
	Magazine.prototype.selectedIndex = -1;

	Magazine.prototype.init = function() {
		SectionSwitcher.prototype.init.call(this);
		this.on('sectionselected', function(newIndex, currentIndex, flipped) {
			this.updateTitle(newIndex, flipped);
		}.bind(this));
		this.trigger('sectionselected', 0, false);
		this.onSwitchEnd(0, false);
	};

	Magazine.prototype.flipCurrentPoster = function() {
		var $currentPage = this.$elements.eq(this.currentIndex);
		var end = curry(this.onSwitchEnd.bind(this), this.currentIndex, !this.isFlipped);
		if (this.isBusy) {
			return;
		}
		this.isBusy = true;
		this.trigger('sectionselected', this.currentIndex, this.currentIndex, !this.isFlipped);
		seq(curry($.fn.toggleClass.bind($currentPage), 'flip'), 0, end, 500).run();
	};

	Magazine.prototype.openCurrentPoster = function() {
		var $currentPage = this.$elements.eq(this.currentIndex);
		if (!$currentPage.hasClass('open')) {
			this.openPoster($currentPage, curry(this.onSwitchEnd.bind(this), this.currentIndex, this.isFlipped));
		}
	};

	Magazine.prototype.closeCurrentPoster = function() {
		var $currentPage = this.$elements.eq(this.currentIndex);
		if ($currentPage.hasClass('open')) {
			this.closePoster($currentPage, curry(this.onSwitchEnd.bind(this), this.currentIndex, this.isFlipped));
		}
	};

	Magazine.prototype.closePoster = function($page, end) {
		if (this.isBusy) {
			return;
		}
		var remC = $.fn.removeClass.bind($page);
		this.isBusy = true;
		if ($page.hasClass('magazine-centerfold')) {
			seq(curry(remC, 'open-top'), 0, curry(remC, 'mid-open'), 500, curry(remC, 'open'), 500, end, 0).run(); 
		}
		else {
			seq(curry(remC, 'open'), 0, end, 500).run();
		}
	};

	Magazine.prototype.openPoster = function($page, end) {
		if (this.isBusy) {
			return;
		}
		var addC = $.fn.addClass.bind($page);
		this.isBusy = true;
		if ($page.hasClass('magazine-centerfold')) {
			seq(curry(addC, 'open'), 0, curry(addC, 'mid-open'), 500, curry(addC, 'open-top'), 500, end, 0).run();
		}
		else {
			seq(curry(addC, 'open'), 0, end, 500).run();
		}
		/*
		//$page.css('display','relative').addClass('open').data('has-opened',true);
		if ($page.hasClass('magazine-centerfold')) {
			setTimeout(function() { $page.addClass('open-top'); }, 1000);
			setTimeout(function() { $page.addClass('mid-open'); }, 500);
			flipped && setTimeout(function() { $page.addClass('flip'); }, 2000);
		}
		else {
			flipped && setTimeout(function() { $page.addClass('flip'); }, 1000);
		}
		*/
	};

	Magazine.prototype.showPoster = function($page, end) {
		var prepare = curry($.fn.css.bind($page), { display:'block' });
		var show = curry($.fn.css.bind($page), { opacity:'1' });
		seq(prepare, 0, show, 64, end, 1000).run();
	};

	Magazine.prototype.switchPosters = function($newPage, $oldPage, end) {
		console.log('Magazine.switchPosters --- $newPage:', $newPage, '$oldPage', $oldPage);
		var hide = curry($.fn.css.bind($oldPage), { opacity:'0' });
		var kill = curry($.fn.css.bind($oldPage), { display:'none', position:'absolute' });
		var show = curry(this.showPoster.bind(this), $newPage, end);
		seq(hide, 0, kill, 1000, show, 0).run();
		//$newPage.css('display','block');
	};

	Magazine.prototype.transition = function(newIndex, flipped) {
		if (this.isBusy) {
			return;
		}
		var $page = this.$elements.eq(newIndex);
		var end = curry(this.onSwitchEnd.bind(this), newIndex, flipped);
		this.isBusy = true;
		this.trigger('sectionselected', newIndex, this.currentIndex, flipped);
		$page.toggleClass('flip', flipped);
		if (this.isValidIndex(this.currentIndex)) {
			this.switchPosters($page, this.$elements.eq(this.currentIndex), end);
		}
		else {
			this.showPoster($page, end);
		}
	};

	Magazine.prototype.validateSwitch = function(newIndex, flipped) {
		if (this.isBusy) {
			console.log("Switch rejected. We have not finished the last one.");
			return false;
		}
		if (newIndex === this.currentIndex && flipped === this.isFlipped) {
			console.warn("Trying to switch to the current poster.");
			return false;
		}
		if (!this.isValidIndex(newIndex)) {
			console.error("Bad index", newIndex);
			return false;
		}
		return true;
	};

	Magazine.prototype.switchByIndex = function(newIndex, flipped) {
		flipped = Boolean(flipped);
		console.log('Magazine.switchByIndex', 'newIndex', newIndex, 'flipped', flipped, 'currentIndex', this.currentIndex, 'isFlipped', this.isFlipped);
		if (!this.validateSwitch(newIndex, flipped)) {
			return;
		}
		newIndex === this.currentIndex ? this.flipCurrentPoster() : this.transition(newIndex, flipped);
	};
	
	Magazine.prototype.switchByHash = function(hash, flipped) {
		console.log('Magazine.switchByHash', 'hash', hash, 'flipped', flipped);
		return this.switchByIndex(this.$elements.index($(hash)), Boolean(flipped));
	};

	Magazine.prototype.updateTitle = function(newIndex, flipped) {
		var $page = this.$elements.eq(newIndex);
		var titles = $page.data('titles').split(',');
		var size = $page.data('poster-size');
		this.$title.text(titles[flipped ? 1 : 0] + ' (' + size.replace(',', '" x ') + '")');
	};

	Magazine.prototype.onSwitchEnd = function(newIndex, flipped) {
		console.log('Magazine.onSwitchEnd --- newIndex', newIndex, 'flipped', flipped);
		this.trigger('sectionswitched', newIndex, this.currentIndex, flipped);
		this.isBusy = false;
		this.currentIndex = newIndex;
		this.isFlipped = flipped;
	};

	return Magazine;

});

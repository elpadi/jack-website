define(['lib/ui/SectionSwitcher','lib/fn/bind'], function(SectionSwitcher, bind) {

	function Magazine($container) {
		SectionSwitcher.call(this, $container);
	}

	function A() {}
	A.prototype = SectionSwitcher.prototype;
	Magazine.prototype = new A();
	Magazine.prototype.constructor = Magazine;

	Magazine.prototype.isFlipped = false;

	Magazine.prototype.openPoster = function($page, flipped) {
		$page.css('display','relative').addClass('open').data('has-opened',true);
		if ($page.hasClass('magazine-centerfold')) {
			setTimeout(function() { $page.addClass('open-top'); }, 1000);
			setTimeout(function() { $page.addClass('mid-open'); }, 500);
			flipped && setTimeout(function() { $page.addClass('flip'); }, 2000);
		}
		else {
			flipped && setTimeout(function() { $page.addClass('flip'); }, 1000);
		}
	};

	Magazine.prototype.showPoster = function($page, flipped, isPosterSwitch) {
		console.log('Magazine.showPoster', '$page', $page);
		var hasOpened = $page.data('has-opened') === true;
		if (isPosterSwitch) {
			hasOpened && $page.toggleClass('flip', flipped);
			$page.css({ opacity:'1' });
			setTimeout(function() {
				!hasOpened && this.openPoster($page, flipped);
			}.bind(this), 1000);
		}
		else {
			!hasOpened && this.openPoster($page, flipped);
		}
	};

	Magazine.prototype.switchPosters = function($newPage, flipped, $oldPage) {
		console.log('Magazine.switchPosters', '$oldPage', $oldPage);
		$oldPage.css({ opacity:'0' });
		setTimeout(function() {
			$oldPage.css({ display:'none', position:'absolute' });
			this.showPoster($newPage, flipped, true);
		}.bind(this), 1000);
		$newPage.css('display','block');
	};

	Magazine.prototype.transition = function(newIndex, flipped) {
		var $page = this.$elements.eq(newIndex);
		if (newIndex === this.currentIndex) {
			$page.toggleClass('flip');
		}
		else {
			this.isFlipped = false;
			if (this.isValidIndex(this.currentIndex)) {
				this.switchPosters($page, flipped, this.$elements.eq(this.currentIndex));
			}
			else {
				this.showPoster($page, flipped, false);
			}
		}
		this.onSwitchEnd(newIndex, flipped);
	};

	Magazine.prototype.validateSwitch = function(newIndex, flipped) {
		if (this.isBusy) {
			console.log("Switch rejected. We have not finished the last one.");
			return false;
		}
		if ((newIndex === this.currentIndex) && (flipped === this.isFlipped)) {
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
		if (!this.validateSwitch(newIndex, flipped)) {
			return;
		}
		console.log('Magazine.switchByIndex', 'newIndex', newIndex, 'flipped', flipped, 'currentIndex', this.currentIndex, 'isFlipped', this.isFlipped);
		this.isBusy = true;
		this.trigger('sectionselected', newIndex, this.currentIndex, flipped);
		this.transition(newIndex, flipped);
	};
	
	Magazine.prototype.switchByHash = function(hash, flipped) {
		console.log('Magazine.switchByHash', 'hash', hash, 'flipped', flipped);
		return this.switchByIndex(this.$elements.index($(hash)), Boolean(flipped));
	};

	Magazine.prototype.onSwitchEnd = function(newIndex, flipped) {
		this.trigger('sectionswitched', newIndex, this.currentIndex, flipped);
		this.isBusy = false;
		this.currentIndex = newIndex;
		this.isFlipped = flipped;
	};

	return Magazine;

});

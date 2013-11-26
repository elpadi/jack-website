define(['lib/ui/SectionSwitcher'], function(SectionSwitcher) {

	function Magazine($container) {
		SectionSwitcher.call(this, $container);
	}

	function A() {}
	A.prototype = SectionSwitcher.prototype;
	Magazine.prototype = new A();
	Magazine.prototype.constructor = Magazine;

	Magazine.prototype.isFlipped = false;

	Magazine.prototype.openPoster = function($page, flipped) {
		$page.css('display','relative').addClass('open');
		if ($page.hasClass('magazine-centerfold')) {
			setTimeout(function() { $page.addClass('open-top'); }, 1000);
			flipped && setTimeout(function() { $page.addClass('flip'); }, 2000);
		}
		else {
			flipped && setTimeout(function() { $page.addClass('flip'); }, 1000);
		}
	};

	Magazine.prototype.showPoster = function($page, flipped) {
		console.log('Magazine.showPoster', '$page', $page);
		var _this = this;
		if ($page.css('display') !== 'block') {
			$page.fadeIn(500, function() {
				_this.openPoster($page, flipped);
			});
		}
		else {
			_this.openPoster($page, flipped);
		}
	};

	Magazine.prototype.switchPosters = function($newPage, flipped, $oldPage) {
		console.log('Magazine.switchPosters', '$oldPage', $oldPage);
		var _this = this;
		$oldPage.fadeOut(500, function() {
			$oldPage.css('position','absolute');
			_this.showPoster($newPage, flipped);
		});
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
				this.showPoster($page, flipped);
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
		console.log('Magazine.switchByHash', 'hash', hash);
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

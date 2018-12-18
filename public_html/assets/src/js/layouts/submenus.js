function Submenus() {
}

Object.defineProperty(Submenus.prototype, 'init', {
	value: function init() {
		var $buttons = $('.main-nav').find('a');
		var texts = _.invoke(_.invoke(_.map($buttons, _.property('textContent')), 'trim'), 'toLowerCase');
		$('.submenu').each(function(i, node) {
			var index = texts.indexOf(node.dataset.parent);
			if (index > -1) Submenu.create(node, $buttons[index]);
		});
	}
});

function Submenu(node, triggerNode) {
	this.node = node;
	this.trigger = triggerNode;
	this.isVisible = false;
	this.isPermanentlyVisible = false;
	this.timeoutId = 0;
	this.init();
}

Object.defineProperty(Submenu.prototype, 'TIMEOUT_DURATION', {
	value: 200
});

Object.defineProperty(Submenu, 'create', {
	value: function create(node, triggerNode) {
		return new Submenu(node, triggerNode);
	}
});

Object.defineProperty(Submenu.prototype, 'toggle', {
	value: function toggle() {
		this[this.isPermanentlyVisible ? 'hide' : 'showPermanently'].call(this);
	}
});

Object.defineProperty(Submenu.prototype, 'resettHideTimeout', {
	value: function resettHideTimeout() {
		clearTimeout(this.timeoutId);
		this.timeoutId = setTimeout(this.onHideTimeout.bind(this), this.TIMEOUT_DURATION);
	}
});

Object.defineProperty(Submenu.prototype, 'showTemporarily', {
	value: function showTemporarily() {
		this.isVisible = true;
		this.node.classList.add('visible');
		clearTimeout(this.timeoutId);
	}
});

Object.defineProperty(Submenu.prototype, 'showPermanently', {
	value: function showPermanently() {
		this.showTemporarily();
		this.isPermanentlyVisible = true;
	}
});

Object.defineProperty(Submenu.prototype, 'onHideTimeout', {
	value: function onHideTimeout() {
		if (!this.isPermanentlyVisible) this.hide();
	}
});

Object.defineProperty(Submenu.prototype, 'hide', {
	value: function hide() {
		this.isVisible = false;
		this.isPermanentlyVisible = false;
		this.node.classList.remove('visible');
		clearTimeout(this.timeoutId);
	}
});

Object.defineProperty(Submenu.prototype, 'init', {
	value: function init() {
		// debounce the function to prevent tap / click conflicts
		var toggle = _.debounce(this.toggle.bind(this), 200, true);
		this.trigger.addEventListener('click', function(e) {
			e.preventDefault();
			if ($(e.target).data('wasTapFired')) return;
			console.log('Submenu click');
			toggle();
		}.bind(this));
		$(this.trigger).enableTapEvent().on('tap', function(e) {
			console.log('Submenu tap');
			e.preventDefault();
			toggle();
		}.bind(this));
		this.trigger.addEventListener('mouseover', function(e) {
			this.showTemporarily();
		}.bind(this));
		this.trigger.addEventListener('mouseout', function(e) {
			this.resettHideTimeout();
		}.bind(this));
		$(this.node).on('mouseenter', function(e) {
			clearTimeout(this.timeoutId);
		}.bind(this));
		$(this.node).on('mouseleave', function(e) {
			this.resettHideTimeout();
		}.bind(this));
		if (window.innerWidth >= 670) { 
			this.node.children[0].style.left = this.trigger.getBoundingClientRect().left + 'px';
		}
	}
});

App.instance.addChild('submneus', new Submenus());

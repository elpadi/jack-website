function Checkout() {
	this.isBusy = false;
}

Object.defineProperty(Checkout.prototype, 'init', {
	value: function init() {
		this.addButton();
	}
});

Object.defineProperty(Checkout.prototype, 'addButton', {
	value: function addButton() {
		var container = document.getElementById('button-container');
		this.button = document.createElement('button');
		this.button.innerHTML = 'Pay $' + container.dataset.amount + ' with Square';
		this.button.className = 'btn';
		this.button.addEventListener('click', this.beginCheckout.bind(this));
		container.appendChild(this.button);
	}
});

Object.defineProperty(Checkout.prototype, 'beginCheckout', {
	value: function beginCheckout() {
		if (!this.isBusy) {
			this.beforeFetch().then(function() {
				App.instance.fetch(location.href).then(this.dataHandler.bind(this));
			}.bind(this));
		}
	}
});

Object.defineProperty(Checkout.prototype, 'dataHandler', {
	value: function dataHandler(data) {
		this.isBusy = false;
		this.button.classList.remove('loading');
		this.button.removeAttribute('disabled');
		if (('errors' in data.response) && data.response.errors.length) {
			alert("Checkout Errors:\n" + data.response.errors.map(function(e) {
				return e.detail + ' On field ' + e.field;
			}).join("\n"));
		}
		else {
			if ('checkout_url' in data.response) location = data.response.checkout_url;
			else alert("Unspecified checkout error. Please try again.");
		}
	}
});

Object.defineProperty(Checkout.prototype, 'beforeFetch', {
	value: function beforeFetch() {
		this.isBusy = true;
		this.button.classList.add('loading');
		this.button.setAttribute('disabled','disabled');
		return App.instance.delayPromise(500);
	}
});

App.instance.addChild('checkout', new Checkout());

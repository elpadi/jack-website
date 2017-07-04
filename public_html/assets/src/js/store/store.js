function Store() {
	this.isBusy = false;
}

Object.defineProperty(Store, 'moneyFormat', {
	value: function moneyFormat(value) {
		return '$' + value.toFixed(2);
	}
});

Object.defineProperty(Store.prototype, 'init', {
	value: function init() {
		$('.store-form').on('submit', this.onFormSubmit.bind(this));
		$('.update-count-form').find('.count').on('change', function(e) {
			$(this.form).trigger('submit');
		});
	}
});

Object.defineProperty(Store.prototype, 'onFormSubmit', {
	value: function onFormSubmit(e) {
		var form = e.currentTarget;
		e.preventDefault();
		if (!this.isBusy) {
			this.beforeFetch(form)
				.then(App.instance.submitForm(form).then(_.bind(this.formDataHandler, this, form)))
				.then(_.bind(this.reset, this, form));
		}
	}
});

Object.defineProperty(Store.prototype, 'formDataHandler', {
	value: function formDataHandler(form, data) {
		_(document.forms).filter(function(f) {
			return ('count' in f) && ('variant_id' in f) && (f.variant_id.value === data.variant_id);
		}).forEach(function(f) {
			console.log(f.count);
			f.count.value = data.item_count;
		});
		$('input[value="' + data.variant_id + '"]').closest('form').find('.count').prop('value', data.item_count);
		// total count body data attribute
		document.body.dataset.cartCount = data.cart_count;
		$('.cart__count').html(data.cart_count);
		$('.cart__subtotal').html(Store.moneyFormat(data.subtotal));
		$('.cart__shipping').html(Store.moneyFormat(data.shipping));
		$('.cart__total').html(Store.moneyFormat(data.subtotal + data.shipping));
		$('.cart__count').html(data.cart_count);
		$('.store-item')
			.filter(function(i, node) { return node.dataset.variantId === data.variant_id; })
			.each(function(i, node) { node.dataset.cartCount = data.item_count; });
	}
});

Object.defineProperty(Store.prototype, 'reset', {
	value: function reset(form) {
		this.isBusy = false;
		form.classList.remove('loading');
		$(form).find('input[type="submit"]').attr('disabled',null);
	}
});

Object.defineProperty(Store.prototype, 'beforeFetch', {
	value: function beforeFetch(form) {
		this.isBusy = true;
		setTimeout(function() { form.classList.add('loading'); }, 200);
		$(form).find('input[type="submit"]').attr('disabled','disabled');
		return App.instance.delayPromise(1000);
	}
});

App.instance.addChild('store', new Store());

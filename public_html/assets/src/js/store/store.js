/**
 * Store Class
 *
 * TODO: Create Cart class for shopping cart code
 */
function Store() {
	this.isBusy = false;
}

Object.defineProperty(Store, 'moneyFormat', {
	value: function moneyFormat(value) {
		return '$' + (value == Math.round(value) ? value : value.toFixed(2));
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
			this.beforeFetch(form).then(function() {
				App.instance.submitForm(form)
					.then(_.bind(this.formDataHandler, this, form))
					.then(_.bind(this.reset, this, form));
			}.bind(this));
		}
	}
});

Object.defineProperty(Store.prototype, 'formDataHandler', {
	value: function formDataHandler(form, data) {
		console.log('Store.formDataHandler', 'data:', data);

		let item = $('.store-item[data-variant-id="' + data.variant_id + '"]');

		if (item.length && data.item_count != -1) {
			if (data.item_count) {
				item[0].dataset.cartCount = data.item_count;
				item.find('.count').prop('value', data.item_count);
			}
			else item.remove();
		}

		// total count body data attribute
		document.body.dataset.cartCount = data.cart_count;

		if (document.body.dataset.path == 'cart') this.updateCartLayout();
	}
});

Object.defineProperty(Store.prototype, 'updateCartLayout', {
	value: function updateCartLayout(data) {
		// update cart table values
		$('.cart__count').html(data.cart_count).attr('data-count', data.cart_count);
		$('.cart__subtotal').html(Store.moneyFormat(data.subtotal.net));
		$('.cart__shipping').html(Store.moneyFormat(data.shipping));
		$('.cart__total').html(Store.moneyFormat(data.subtotal.net + data.shipping));

		// update discount status
		if (data.subtotal.gross == data.subtotal.net) {
			$('.store-item[data-variant-id="discount"]').remove();
			document.body.dataset.discount = 0;
		}
		else {
			document.body.dataset.discount = data.subtotal.gross - data.subtotal.net;
			if ($('.store-item[data-variant-id="discount"]').length == 0) {
				Store.createCartDiscountItem(data);
			}
		}
	}
});

Object.defineProperty(Store, 'createCartDiscountItem', {
	value: function createCartDiscountItem(data) {
		let last = $('.store-item').last();
		if (last.length == 0) return;
		let d = last.clone().get(0);

		d.dataset.variantId = 'discount';
		d.dataset.cartCount = '1';
		d.querySelector('aside').innerHTML = '';
		d.querySelector('h2').innerHTML = data.discount + '% Discount';
		d.querySelector('p').innerHTML = '-' + Store.moneyFormat(data.subtotal.gross - data.subtotal.net);
		d.querySelectorAll('td')[2].innerHTML = '&nbsp;';

		let f = d.querySelector('form');
		f.action = f.action.replace('update', 'remove-discount');
		$('input[type="hidden"]', f).remove();

		last.after(d);
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
		form.classList.add('loading');
		$(form).find('input[type="submit"]').attr('disabled','disabled');
		return App.instance.delayPromise(500);
	}
});

App.instance.addChild('store', new Store());

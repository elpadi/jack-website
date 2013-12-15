require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"eventemitter": "eventemitter2/lib/eventemitter2",
		"lib": "js-libs"
	}
});

function getIssueImageFromInputName(issue, name) {
	switch (name) {
		case 'FrontCover':
			return issue.covers.front;
		case 'BackCover':
			return issue.covers.back;
		case 'Index':
			return issue.covers.index;
		case 'CoverPoster':
			return issue.covers.poster;
		case 'FrontCenterfold':
			return issue.centerfold.front;
		case 'BackCenterfold':
			return issue.centerfold.back;
	}
	return '';
}

require(['jquery','lib/ui/SectionSwitcher','lib/ui/SectionSwitcher/SectionLinks'], function(jquery, SectionSwitcher, SectionLinks) {
	$('.tabs').each(function(i, el) {
		var tabs = new SectionSwitcher($(el));
		tabs.addComponent(SectionLinks);
		tabs.init();
	});
});

require(['jquery','dropzone/downloads/dropzone-amd-module'], function(jquery, Dropzone) {
	$('.admin-form-image').each(function(i, el) {
		var dropzone = new Dropzone(el, {
			paramName: el.getAttribute('data-file-input-name'),
			uploadMultiple: false,
			acceptedFiles: "image/jpeg",
		});
		dropzone.on('success', function(file, response) {
			if (response.success) {
				var src = getIssueImageFromInputName(response.issue, el.getAttribute('data-file-input-name'));
				console.log('Issue image uploaded.', response);
				$(el).closest('.issue-image').find('.issue-image__image img').attr('src', src);
			}
			else {
				alert("Error: " + response.error);
			}
		});
	});
});

require(['jquery','underscore','eventemitter'], function(jquery, underscore, EventEmitter) {
		$.fn.ajaxForm = function(success, error) {
			return this.filter('form').each(function(i, el) {
				$(el).on('submit', function(e) {
					var $form = $(el);
					var settings = $.extend({
						url: $form.attr('action'),
						data: $form.serialize(),
						type: $form.attr('method'),
						error: error,
						success: success
					}, $form.data());
					e.preventDefault();
					$.ajax(settings);
					return false;
				});
			});
		};
	function SwappableTable($table) {
		this.$table = $table;
		this.$rows = $table.find('tbody').find('tr');
		this.$cells = $table.find('td');

		this.$table.on('click', 'td', this.onCellClick.bind(this));

		this.$form = $(document.page_order);
		this.setupForm();

		this.$flash = $('#page-notices');
	}

	function A() {}
	A.prototype = $.extend({}, EventEmitter.prototype);
	SwappableTable.prototype = new A();
	SwappableTable.prototype.constructor = SwappableTable;

	SwappableTable.prototype.flashDuration = 5000;

	SwappableTable.prototype.onCellClick = function(e) {
		var $td = $(e.currentTarget);
		var $row = $td.closest('tr');
		if ($td.prev().length === 0) {
			this.selectRow($row);
		}
		else {
			this.selectCell($td);
		}
	};

	SwappableTable.prototype.selectRow = function($row) {
		var $selectedRow = this.$rows.filter('.selected');
		if ($selectedRow.length === 1 && $selectedRow[0] !== $row[0]) {
			this.swapRows($row, $selectedRow);
			this.emit('rowswap', $row, $selectedRow);
		}
		else {
			$row.toggleClass('selected');
		}
		this.$cells.removeClass('selected');
	};

	SwappableTable.prototype.selectCell = function($cell) {
		var $selectedCell = this.$cells.filter('.selected');
		if ($selectedCell.length === 1 && $selectedCell[0] !== $cell[0]) {
			this.swapCells($cell, $selectedCell);
			this.emit('cellswap', $cell, $selectedCell);
		}
		else {
			$cell.toggleClass('selected');
		}
		this.$rows.removeClass('selected');
	};

	SwappableTable.prototype.swapRows = function($rowA, $rowB) {
		var $cellsA = $rowA.find('td').not(':first-child');
		var $cellsB = $rowB.find('td').not(':first-child');
		for (var i = 0; i < $cellsA.length; i++) {
			this.swapCells($cellsA.eq(i), $cellsB.eq(i));
		}
		this.$rows.removeClass('selected');
	};

	SwappableTable.prototype.swapCells = function($cellA, $cellB) {
		var $contentA = $cellA.find('.swappable-table__cell-content');
		var $contentB = $cellB.find('.swappable-table__cell-content');
		$cellA.append($contentB);
		$cellB.append($contentA);
		this.$cells.removeClass('selected');
	};

	SwappableTable.prototype.getOrder = function() {
		return $.map(this.$rows, function(tr, i) {
			var $row = $(tr);
			return {
				front: document.page_order['row' + i + '_front'].value,
				back: document.page_order['row' + i + '_back'].value
			};
		});
	};

	SwappableTable.prototype.setupForm = function() {
		var error = function(/*args*/) {
			this.flashError('Could not save the poster order.');
			console.error.apply(console, Array.prototype.slice.call(arguments, 0));
		}.bind(this);
		this.$form.ajaxForm(function(data, textStatus, jqXHR) {
			if (data.success) {
				this.flash('Poster order saved.');
			}
			else {
				error(data.error);
			}
		}, function(jqXHR, textStatus, errorMsg) {
			error(jqXHR, textStatus, errorMsg);
		});
	};

	SwappableTable.prototype.flash = function(msg, msgType) {
		if (msgType === undefined) {
			msgType = 'notice';
		}
		var $p = $(document.createElement('p')).addClass(msgType).text(msg);
		this.$flash.append($p);
		setTimeout(function() { $p.remove(); }, this.flashDuration);
	};

	SwappableTable.prototype.flashError = function(msg) {
		this.flash(msg, 'error');
	};

	var table = new SwappableTable($('.swappable-table'));
	table.on('cellswap', function($cellA, $cellB) {
		$cellA.find('input').val($cellA.find('.poster').data('poster-id'));
		$cellB.find('input').val($cellB.find('.poster').data('poster-id'));
		table.$form.trigger('submit');
	});
	table.on('rowswap', function($rowA, $rowB) {
		$rowA.find('td').each(function(i, cell) {
			var $cell = $(cell);
			$cell.find('input').val($cell.find('.poster').data('poster-id'));
		});
		$rowB.find('td').each(function(i, cell) {
			var $cell = $(cell);
			$cell.find('input').val($cell.find('.poster').data('poster-id'));
		});
		table.$form.trigger('submit');
	});
});

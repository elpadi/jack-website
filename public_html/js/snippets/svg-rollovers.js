(function() {

	var setSvgClass = function(obj, value, replace) {
		updateSvg(obj, function() {
			var svg = obj.contentDocument.querySelector('svg');
			svg.setAttribute('class', replace ? svg.className.baseVal.replace(replace, value) : svg.className.baseVal + ' ' + value);
		});
	};

	var updateSvg = function(obj, fn) {
		if (obj.contentDocument && obj.contentDocument.readyState === 'complete' && obj.contentDocument.querySelector('svg')) fn();
		else obj.addEventListener('load', fn);
	};

	Array.from(document.getElementsByClassName('svg-button')).filter(button => button.dataset.section !== 'home').forEach(function(button) {
		var obj = button.children[0];
		obj.parentNode.addEventListener('mouseover', function(e) { setSvgClass(obj, 'over'); });
		obj.parentNode.addEventListener('mouseout', function(e) { setSvgClass(obj, '', 'over'); });
	});
	Array.from(document.getElementsByClassName('svg-image')).forEach(function(obj) {
		if (obj.parentNode.classList.contains('selected')) setSvgClass(obj, 'selected');
	});
	Array.from(document.getElementById('masthead').getElementsByClassName('svg-image')).filter(function(obj) {
		return window.getComputedStyle(obj.parentNode)["background-color"].includes('0.');
	}).forEach(function(obj) { setSvgClass(obj, 'selected'); });

})();

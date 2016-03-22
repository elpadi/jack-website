Array.from(document.getElementsByClassName('svg-image')).forEach(function(obj) {
	obj.parentNode.addEventListener('mouseover', function(e) {
		obj.contentDocument.querySelector('svg').setAttribute('class', 'over');
	});
	obj.parentNode.addEventListener('mouseout', function(e) {
		obj.contentDocument.querySelector('svg').setAttribute('class', '');
	});
	if (obj.parentNode.classList.contains('selected'))
		obj.contentDocument.querySelector('svg').setAttribute('class', 'over');
});

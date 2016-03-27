window.addEventListener('load', function() {
	Array.from(document.getElementsByClassName('fade')).forEach(function(node) { node.classList.add('visible'); });
	setTimeout(function() { document.querySelector('.cover').classList.remove('visible'); }, 5000);
	setTimeout(function() { document.querySelector('.intro').classList.remove('visible'); }, 11000);
});

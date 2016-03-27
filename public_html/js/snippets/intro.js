window.addEventListener('load', function() {
	Array.from(document.getElementsByClassName('intro-fade')).forEach(function(node) { node.classList.add('visible'); });
	Array.from(document.getElementsByClassName('post-intro-fade')).forEach(function(node) { node.classList.add('visible'); });
	setTimeout(function() { document.querySelector('.posters').classList.remove('visible'); }, 5000);
	setTimeout(function() { document.querySelector('.flags').classList.remove('visible'); }, 11000);
	setTimeout(function() { document.querySelector('.logo').classList.remove('visible'); }, 17000);
});

class IssueSection {

	constructor(section) {
		this.section = section;
		this.images = section.getElementsByTagName('img');
		this.main = section.querySelector('main');
		this.text = this.main.children[0];
		this.maxScroll = 0;
		this.landscapeCount = 0;
	}

	init() {
		this.classifyImages();
		const row = this.landscapeCount	+ 1,
			span = this.images.length - this.landscapeCount;
		this.main.style.gridRow = `${row} / span ${span}`;
		setTimeout(this.computeMaxScroll.bind(this), 100);
		setTimeout(() => this.section.classList.add('visible'), 100);
		this.section.parentElement.classList.add('loaded');
	}

	classifyImages() {
		for (let i = 0, l = this.images.length; i < l; i++) {
			const img = this.images[i];
			if (img.naturalWidth / img.naturalHeight > 2) {
				img.classList.add('landscape');
				this.landscapeCount++;
			}
			else img.classList.add('portrait');
		}
	}

	computeMaxScroll() {
		const outer = this.section.getBoundingClientRect();
		const inner = this.text.getBoundingClientRect();
		this.maxScroll = Math.max(0, outer.bottom - inner.bottom);
		this.offsetTop = this.main.offsetTop;
		setTimeout(this.repositionText.bind(this), 100);
	}

	repositionText() {
		const y = Math.min(Math.max(0, window.scrollY - this.offsetTop), this.maxScroll);
		console.log('IssueSection.repositionText', window.scrollY, this.offsetTop, this.maxScroll, y, this.section.dataset.slug);
		this.text.style.transform = `translateY(${y}px)`;
	}

}

module.exports = {
	load: function() {
		this.sections = Array.from(document.querySelectorAll('.issue-section')).map(s => new IssueSection(s));
		this.sections.forEach(s => s.init());
	},
	scroll: function() {
		this.sections.forEach(s => s.repositionText());
	},
	resize: function() {
		this.sections.forEach(s => s.computeMaxScroll());
	}
};

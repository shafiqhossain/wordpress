document.addEventListener('DOMContentLoaded', function () {
	var $j = jQuery;
	var $carousel = $j('.project-panel-slider-inner');
	if ($carousel.length) {
		$carousel.slick();
	}
});
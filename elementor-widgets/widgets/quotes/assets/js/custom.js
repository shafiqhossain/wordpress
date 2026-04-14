document.addEventListener('DOMContentLoaded', function () {
	var $j = jQuery;
	var $carousel = $j('.quotes-panel-slider');
	if ($carousel.length) {
		$carousel.slick();
	}
});

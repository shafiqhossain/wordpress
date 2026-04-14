document.addEventListener('DOMContentLoaded', function () {
	var $j = jQuery;
	var $carousel = $j('.cmma-hero-carousel');
	if ($carousel.length) {
		$carousel.slick();
	}
});
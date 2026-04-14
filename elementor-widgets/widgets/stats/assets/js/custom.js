document.addEventListener('DOMContentLoaded', function () {
	window.$j = window.$j || jQuery;
	var $carousel = $j('.stats-slider');
	if ($carousel.length) {
		$carousel.slick();
	}
});

document.addEventListener('DOMContentLoaded', function() {
	var $j = jQuery;

	window.addEventListener('cmma-modal-open', function(event) {
		var $modal = event.detail.$element;

		if ($modal.length) {
			var $slider = $modal.find('.gallery-slider');

			if ($slider.length && !$slider.hasClass('.slick-initialized')) {

				$slider.find('.gallery-slide-item').each(function() {
					var height = $j(this).find('.gallery-slide-image').height() - $j(this).find('.gallery-slider-controls').height() - 10;

					$j(this).find('.gallery-slide-body').height(height);
				});

				$slider.slick();

				$slider.on('click', '.gallery-slider-arrow', function() {
					$slider.slick($j(this).hasClass('cmma-prev-btn') ? 'slickPrev' : 'slickNext');
				});
			}
		}
	});
});

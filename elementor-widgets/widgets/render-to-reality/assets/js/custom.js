window.addEventListener('DOMContentLoaded', function() {
	window.$j = window.$j || jQuery;

	$j('.render-reality-asset-comparison').each(function() {
		$j(this).find('.render-reality-asset-resize img').css({ width: $j(this).width() + 'px' });

		imageComparison(
			$j(this).find('.render-reality-asset-divider'),
			$j(this).find('.render-reality-asset-resize'),
			$j(this)
		);
	});

	function imageComparison(dragElement, resizeElement, container) {
		var touched = false;

		window.addEventListener('touchstart', function () {
			touched = true;
		});

		window.addEventListener('touchend', function () {
			touched = false;
		});

		dragElement.on('mousedown touchstart', function (e) {
			dragElement.addClass('draggable');
			resizeElement.addClass('resizable');

			var dragWidth = dragElement.outerWidth(),
				posX = dragElement.offset().left + dragWidth - (e.pageX ? e.pageX : e.originalEvent.touches[0].pageX),
				containerOffset = container.offset().left,
				containerWidth = container.outerWidth(),
				minLeft = containerOffset + 10,
				maxLeft = containerOffset + containerWidth - dragWidth - 10;

			dragElement.parents().on('mousemove touchmove', function (e) {

				if (touched === false) {
					e.preventDefault();
				}

				var moveX = e.pageX ? e.pageX : e.originalEvent.touches[0].pageX,
					leftValue = moveX + posX - dragWidth;

				if (leftValue < minLeft) {
					leftValue = minLeft;
				} else if (leftValue > maxLeft) {
					leftValue = maxLeft;
				}

				var widthValue = (leftValue + dragWidth / 2 - containerOffset) * 100 / containerWidth + '%';

				$j('.draggable').css('left', widthValue).on('mouseup touchend touchcancel', function () {
					$j(this).removeClass('draggable');
					resizeElement.removeClass('resizable');
				});

				$j('.resizable').css('width', widthValue);
			}).on('mouseup touchend touchcancel', function () {
				dragElement.removeClass('draggable');
				resizeElement.removeClass('resizable');
			});
		}).on('mouseup touchend touchcancel', function (e) {
			dragElement.removeClass('draggable');
			resizeElement.removeClass('resizable');
		});
	}
});

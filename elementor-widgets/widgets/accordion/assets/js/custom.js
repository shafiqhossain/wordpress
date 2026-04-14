window.addEventListener('DOMContentLoaded', function () {
	window.$j = window.$j || jQuery;

	$j(document).on('click', '.accordion-item-title', function () {
		$j(this).toggleClass('active');

		$j('.accordion-item-title').not(this).removeClass('active');

		var accordionItem = $j(this).parent('.accordion-item');
		var accordionContent = accordionItem.find('.accordion-item-content');

		if ($j(window).innerWidth() > 992) {
			var contentRight = accordionItem.find('.accordion-item-right');
		}

		accordionContent.slideToggle();

		if (contentRight) {
			contentRight.toggle(); // Changed from slideToggle() to toggle() for no animation
		}

		accordionItem.prevAll('.accordion-item').find('.accordion-item-content').slideUp();
		accordionItem.prevAll('.accordion-item').find('.accordion-item-right').hide(); // Use hide() instead of slideUp() for no animation

		accordionItem.nextAll(".accordion-item").find(".accordion-item-content").slideUp();
		accordionItem.nextAll(".accordion-item").find(".accordion-item-right").hide(); // Use hide() instead of slideUp() for no animation
	});
});

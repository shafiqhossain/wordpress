window.addEventListener('DOMContentLoaded', function () {
	window.$j = window.$j || jQuery;

	// $j(document).on('click', '.service-item-title', function () {
	// 	var $parent = $j(this).parents('.services-list');

	// 	$parent.find('.service-item-content').hide();
	// 	$parent.find('.service-item-content[data-target="' + $j(this).data('target') + '"]').show();
	// 	$parent.addClass('active');

	// 	$parent.find('.service-item').removeClass('active');
	// 	$j(this).parent('.service-item').addClass('active');
	// });

	// $j(document).on('click', '.services-list .close-btn', function () {
	// 	$j(this).parents('.services-list').removeClass('active');
	// });

	// $j(document).on('click', '.services-list .cmma-services-mask', function () {
	// 	$j(this).parents('.services-list').removeClass('active');
	// });

	// $j(document).on('click', function (event) {
	// 	var $target = $j(event.target);
	// 	if (!$target.closest('.service-item, .service-item-inner').length) {
	// 		$j('.services-list').removeClass('active');
	// 	}
	// });
});



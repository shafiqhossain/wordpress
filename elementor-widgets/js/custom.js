window.__cmma = window.__cmma || {};
window.addEventListener('DOMContentLoaded', function () {
	window.$j = jQuery
	let previousUrl = window.location.href;
	if (window.location.hash) {
		previousUrl = window.location.href.split('#')[0];
	}

	const originalTitle = document.title;

	__cmma = {
		modal: {
			show: function ($element, scroll) {
				$element.addClass('cmma-modal-show');

				if (scroll) {
					$j('body').addClass('no-scroll');
				}

				const event = new CustomEvent('cmma-modal-open', {
					detail: {
						$element: $element
					}
				});

				window.dispatchEvent(event);
			},

			hide: function ($element) {
				$element.removeClass('cmma-modal-show');
				$j('body').removeClass('no-scroll');

				const videoElement = $element.find('video');
				if (videoElement.length) {
					videoElement.each(function () {
						this.pause();
						this.currentTime = 0;
					});
				}

				const event = new CustomEvent('cmma-modal-close', {
					detail: {
						$element: $element
					}
				});

				let type = $element.data('type')
				if (type == 'page-popup') {
					let id = $element.data('id')
					$j('.cmma-video-popup-container').hide();
					setCookie(`wp-settings-page-popup-${id}`, 'modal closed', 30);
				}
				window.dispatchEvent(event);
			}
		}
	}

	$j('body').on('click', '.cmma-modal-button', function (e) {
		__cmma.modal.show($j('#' + $j(this).attr('data-modal-id')), !$j(this).hasClass('cmma-modal-scroll'));
		$j('body').addClass('cmma-modal-open');
		e.preventDefault();
	});

	$j('body').on('click', '.cmma-modal-close', function (e) {
		__cmma.modal.hide($j('#' + $j(this).attr('data-modal-id')));
		$j('body').removeClass('cmma-modal-open');

		e.preventDefault();

		document.title = originalTitle;

		history.replaceState(null, null, previousUrl);
	});

	$j(document).click(function (e) {
		if ($j(e.target).is('.widget-modal')) {
			__cmma.modal.hide($j('.widget-modal'));
			$j('body').removeClass('cmma-modal-open');

			document.title = originalTitle;

			history.replaceState(null, null, previousUrl);
		}
	});

	$j(document).keydown(function (e) {
		if (e.keyCode === 27) {
			__cmma.modal.hide($j('.widget-modal'));
			$j('body').removeClass('cmma-modal-open');

			document.title = originalTitle;

			history.replaceState(null, null, previousUrl);
		}
	});

	$j('.wysiwyg-wrapper iframe, cmma-modal-body iframe').wrap('<div class="video-iframe"/>');
	$j('.wysiwyg-wrapper video, cmma-modal-body video').wrap('<div class="video-iframe"/>');

	$j('.wysiwyg-wrapper p, cmma-modal-body p').each(function () {
		if ($j(this).find('iframe, video, img').length > 0) {
			$j(this).addClass('contains-media');
		}
	});

	window.setTimeout(function () {
		jQuery('.image-carousel').slick({
			slidesToShow: 2,
			dots: false,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 5000, // Adjust as needed
			prevArrow: "<button class='prev-btn'></button>",
			nextArrow: "<button class='next-btn'></button",
			responsive: [
				{
					breakpoint: 991,
					settings: {
						slidesToShow: 1
					}
				}
			]
		});

		jQuery('.image-video-slider-image-carousel').slick(
			{
				slidesToShow: 2,
				dots: false,
				infinite: true,
				autoplay: true,
				autoplaySpeed: 5000, // Adjust as needed
				prevArrow: '<button class="prev-btn"><svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentcolor" /></svg></button>',
				nextArrow: '<button class="next-btn"><svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentcolor" /></svg></button>',

				responsive: [
					{
						breakpoint: 991,
						settings: {
							slidesToShow: 1
						}
					}
				],
				onInit: function () {
					scaleActiveImage();
				},
				afterChange: function () {
					scaleActiveImage();
					playCurrentSlideVideo();
				}
			}
		);

		jQuery('.project-perspective-carousel').slick({
			slidesToShow: 3,
			dots: false,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 5000, // Adjust as needed
			prevArrow: "<button class='prev-btn'></button>",
			nextArrow: "<button class='next-btn'></button",
			responsive: [
				{
					breakpoint: 991,
					settings: {
						slidesToShow: 1
					}
				}
			]
		});
		// // mouse button play pause

		jQuery('.mute-btn').click(function () {
			//jQuery(this).toggleClass('active', !jQuery(this).hasClass('active'));
			jQuery(this).toggleClass('active');
			var slider = jQuery('.slider');
			var videoElement = slider.find('.slick-current video')[0];
			if (videoElement) {
				videoElement.muted = !videoElement.muted;
			}
		});

		jQuery('.item').mouseenter(function () {
			jQuery('.custom-controls').fadeIn();
		});

		jQuery('.item').mouseleave(function () {
			jQuery('.custom-controls').fadeOut();
		});


		jQuery(".slider").slick({
			infinite: true,
			arrows: false,
			dots: true,
			autoplay: true,
			speed: 800,
			slidesToShow: 1,
			slidesToScroll: 1,
		});

	}, 1000); // Adjust the delay as needed

	// Open the modal when the document is ready
	jQuery('body').on('click', '.cmma_block_modal_toggle', function () {
		// Your click function code here
		jQuery('body').toggleClass('no-scroll');
		jQuery(this).closest('.img-block-wrapper').toggleClass('active');
	});

	jQuery('body').on('click', '.close', function () {
		jQuery('body').removeClass('no-scroll');
		jQuery(this).closest('.img-block-wrapper').removeClass('active');
	});


	/*triggers */

	jQuery('.slick-next-btn').on('click', function () {
		jQuery('.prev-btn').click();
	});

	jQuery('.slick-prev-btn').on('click', function () {
		jQuery('.next-btn').click();
	});

	function setCookie(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "") + expires + "; path=/";
	}


	jQuery('.cmma-modal-content').on('scroll', function () {
		if ((this.scrollHeight - jQuery(this).scrollTop()) <= jQuery(this).outerHeight() + 10) {
			jQuery(this).closest('.modal').addClass('scroll-end')
		} else {
			jQuery(this).closest('.modal').removeClass('scroll-end')
		}
	});
});

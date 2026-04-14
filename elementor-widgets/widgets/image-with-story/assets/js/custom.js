window.__cmma = window.__cmma || {};

window.addEventListener('DOMContentLoaded', function () {
	window.$j = window.$j || jQuery;

	var __stories = {}

	$j.extend(
		__cmma,
		{
			story: {
				carousel: {
					init: function () {
						$j('.story-slider').each(function () {
							var $modal = $j(this).parents('.story-modal');

							var $slider = $j(this).slick({
								infinite: false,
								arrows: false,
								dots: false,
								autoplay: false,
								speed: 800,
								slidesToShow: 1,
								slidesToScroll: 1,
							});

							__stories[$modal.attr('id')] = {
								slider: $slider,
								progressbar: null
							};

							__cmma.story.bind.events($slider);
						});
					}
				},

				progress: {
					start: function (id) {
						if (__stories[id]) {
							__stories[id]['progressbar'] = setInterval(function () {
								__cmma.story.progress.update(id);
							}, 100);
						}
					},

					stop: function (id) {
						if (__stories[id]) {
							clearInterval(__stories[id]['progressbar']);
						}
					},

					reset: function (id) {
						$j('#' + id + ' .story-progressbar-line').css('--progress-line', '0%');
					},

					update: function (id) {
						if (__stories[id]) {
							var $slider = __stories[id]['slider'];
							var video = $slider.find('.slick-current video')[0];

							if (!video) {
								return;
							}

							var percentTime = (video.currentTime / video.duration) * 100;

							if (percentTime > 1) {
								$j('#' + id)
									.find('.story-progressbar-line[data-slick-index="' + $slider.slick('slickCurrentSlide') + '"]')
									.css('--progress-line', Math.ceil(percentTime) + '%');
							}
						}
					}
				},

				video: {
					process: function ($modal, state) {
						$modal.find('.story-controls .process-btn').attr('state', state);
					}
				},

				bind: {
					events: function ($slider) {
						var $modal = $slider.parents('.story-modal');

						$slider.find('video').on('ended', function () {
							var $slickObject = $slider.slick('getSlick');

							if (($slickObject.slideCount - 1) !== $slickObject.currentSlide) {
								$slider.slick('slickNext');
							} else {
								try {
									__cmma.story.video.process($modal, 'pause');
								} catch (error) {}
							}
						});

						$slider.find('video').on('play', function () {
							try {
								__cmma.story.video.process($modal, 'play');
							} catch (error) {}
						});

						$slider.find('video').on('pause', function () {
							try {
								__cmma.story.video.process($modal, 'pause');
							} catch (error) {}
						});

						$modal.find('.story-progressbar').click(function () {
							var $slideIndexElement = $j(this).find('.story-progressbar-line');
							$slider.slick('slickGoTo', $slideIndexElement.attr('data-slick-index'));
						});

						$modal.find('.process-btn').click(function () {
							var currentVideo = $slider.find('.slick-current video')[0];
							if (currentVideo.paused) {
								try {
									__cmma.story.video.process($modal, 'play');
								} catch (error) {}

								currentVideo.play().catch(
									function (error) {
										console.error('Error playing video:', error.message);
									}
								);
								$modal.find('.story-controls .process-btn').attr('state', 'play');
							} else {
								$modal.find('.story-controls .process-btn').attr('state', 'pause');
								try {
									__cmma.story.video.process($modal, 'pause');
								} catch (error) {}
								currentVideo.pause();
							}
						});

						$modal.find('.sound-btn').click(function () {
							$slider.find('video').prop('muted', !$slider.find('video').prop('muted'));
							$j(this).attr('state', $slider.find('video').prop('muted') ? 'mute' : 'unmute');
						});

						$slider.on('afterChange', function (event, slick, currentSlide) {
							// Stop all videos in the slider
							$slider.find('video').each(function () {
								this.pause();
							});

							var currentVideo = $slider.find('.slick-current video')[0];

							if (currentVideo) {
								currentVideo.play();
								setTimeout(function () {
									try {
										__cmma.story.video.process($modal, 'play');
									} catch (error) {}
								}, 200);
							}
						});
					}
				}
			}
		}
	);

	window.addEventListener('cmma-modal-open', function (event) {
		var $modal = event.detail.$element;

		if ($modal.length) {
			try {
				__cmma.story.progress.start($modal.attr('id'));
			} catch (error) { }

			var currentVideo = $modal.find('.slick-current video')[0];
			if (currentVideo) {
				currentVideo.play();
			}

			try {
				__cmma.story.video.process($modal, 'play');
			} catch (error) {}
		}
	});

	window.addEventListener('cmma-modal-close', function (event) {
		var $modal = event.detail.$element;

		if ($modal.length) {
			try {
				__cmma.story.progress.stop($modal.attr('id'));
			} catch (error) {}
		}
	});

	__cmma.story.carousel.init();
});

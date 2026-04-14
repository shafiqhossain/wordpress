window.addEventListener('DOMContentLoaded', function() {
	var $j = jQuery;
	var $carousel = $j('.cmma-carousel');

	if ($carousel.length) {
		$carousel.slick();

		$carousel.on('beforeChange', function(event, slick) {
			playPauseVideo($j(slick.$slider), 'pause');
		});

		$carousel.on('afterChange', function(event, slick) {
			playPauseVideo($j(slick.$slider), 'play');
		});
	}
});

function postMessageToPlayer(player, command) {
	if (player == null || command == null) {
		return;
	}

	player.contentWindow.postMessage(JSON.stringify(command), '*');
}

function playPauseVideo(slick, control) {
	var currentSlide, slideType, startTime, player, video;

	currentSlide = slick.find('.slick-current');
	player = currentSlide.find('iframe').get(0);

	if (currentSlide.find('.vimeo-embed').length) {
		switch (control) {
			case 'play':
				postMessageToPlayer(player, {
					'method': 'play',
					'value': 1
				});

				break;

			case 'pause':
				postMessageToPlayer(player, {
					'method': 'pause',
					'value': 1
				});

				break;
		}
	} else if (currentSlide.find('.youtube-embed').length) {
		switch (control) {
			case 'play':
				postMessageToPlayer(player, {
					'event': 'command',
					'func': 'mute'
				});

				postMessageToPlayer(player, {
					'event': 'command',
					'func': 'playVideo'
				});

				break;

			case 'pause':
				postMessageToPlayer(player, {
					'event': 'command',
					'func': 'pauseVideo'
				});

				break;
		}
	} else if (currentSlide.find('.video-embed').length) {
		video = currentSlide.children('video').get(0);

		if (video != null) {
			if (control === 'play') {
				video.play();
			} else {
				video.pause();
			}
		}
	}
}

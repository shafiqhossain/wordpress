window.addEventListener('DOMContentLoaded', function () {
  window.$j = window.$j || jQuery;

  new WOW().init();

  $j(document).ready(function () {
    var currentDomain = window.location.hostname; // Get the current site domain

    // console.log('chris-test-member', window.location);

    let hash = window.location.hash.substring(1);

    if (hash && window.location.href.indexOf('/our-people/') > -1) {
      // Construct member link if hash is not found on the page
      let memberLink = `${window.location.origin}/people/${hash}`;
      let fakeLink = document.createElement('a');
      fakeLink.href = memberLink;
      document.body.appendChild(fakeLink);
      fakeLink.click();
      document.body.removeChild(fakeLink);
    }


    $j('a').each(function () {
      var link = $j(this).attr('href');

      // Check if the link is not empty and is a valid URL
      if (link && link.indexOf('http') === 0) {
        var linkDomain = (new URL(link)).hostname;

        // Open the link in a new window if it does not match the current domain
        if (linkDomain.indexOf(currentDomain) === -1) {
          $j(this).attr('target', '_blank');
        }
      }
    });
  });



  $j('body').on('click', '.elementor-search-form__icon', function () {
    $j(this).parents('.elementor-search-form').toggleClass('elementor-search-input__open');
  });

  $j('.cmma-page-slideshow').slick();

  $j('.cmma-page-slideshow').on('beforeChange', function (event, slick) {
    playPauseVideo($j(slick.$slider), 'pause');
  });

  $j('.cmma-page-slideshow').on('afterChange', function (event, slick) {
    playPauseVideo($j(slick.$slider), 'play');
  });

  $j('.control-icon').on('click', function (event, slick) {
    var item = $j(this).closest('.single-hero-slider, .cmma-slide-iframe.youtube-embed');
    var player = item.find('iframe').get(0);
    var embeddedVideo = $j(this).closest('.cmma-embedded-video');

    if (embeddedVideo.length) {
      player = embeddedVideo.find('iframe').get(0);
    }

    // Get the current iframe src
    var videoSrc = $j(player).attr('src').replace('autoplay=0', 'autoplay=1');
    $j(player).attr('src', videoSrc);

    // Hide thumbnail and show video panel with a slight delay
    setTimeout(() => {
      item.find(".youtube-thumb").hide();
      item.find(".panel-video-embed").show();
      item.find(".cmma-video-replay-btn").show();
      item.find(".cmma-video-embed-audio").show();
    }, 300);
  });

  $j('.cmma-video-replay-btn').on('click', function () {
    var player = $j(this).closest('.cmma-embedded-video, .single-hero-slider, .cmma-slide-iframe.youtube-embed')
      .find('iframe')
      .get(0);

    if (player) {
      var isPlayerMuted = $j(this).closest('.cmma-container, .cmma-embedded-video').find('.cmma-video-embed-audio').hasClass('muted');
      var videoSrc = $j(player).attr('src').replace(/autoplay=\d/, 'autoplay=1').replace(/&?mute=\d/, '');
      videoSrc += isPlayerMuted ? '&mute=1' : '&mute=0';
      $j(player).attr('src', videoSrc);
    }
  });

  $j('.cmma-video-embed-audio, .audio-btn-block').on('click', function (event, slick) {
    var isMuted = true
    var player = $j(this).closest('.single-hero-slider, .cmma-slide-iframe.youtube-embed').find('iframe').get(0);
    var embeddedVideo = $j(this).closest('.cmma-embedded-video');
    if (embeddedVideo.length) {
      player = embeddedVideo.find('iframe').get(0);
    }

    var isMuted = $j(this).hasClass('muted');
    if (isMuted) {
      postMessageToPlayer(player, {
        'event': 'command',
        'func': 'unMute'
      });
      $j(this).removeClass('muted')
    } else {
      postMessageToPlayer(player, {
        'event': 'command',
        'func': 'mute'
      });
      $j(this).addClass('muted')
    }
  });

  $j('.gfield input').each(function () {
    $j(this).on('focus', function () {
      if ($j(this).parent('span').length) {
        $j(this).parent().addClass('gfield_active');
      } else {
        $j(this).parents('.gfield').addClass('gfield_active');
      }
    });

    $j(this).on('blur', function () {
      if ($j(this).val().length == 0) {
        if ($j(this).parent('span').length) {
          $j(this).parent('span').removeClass('gfield_active');
        } else {
          $j(this).parents('.gfield').removeClass('gfield_active');
        }
      }
    });

    if ($j(this).val() != '') {
      if ($j(this).parent('span').length) {
        $j(this).parent().addClass('gfield_active');
      } else {
        $j(this).parents('.gfield').addClass('gfield_active');
      }
    }
  });

  $j('.gfield textarea').each(function () {
    $j(this).on('focus', function () {
      if ($j(this).parent('span').length) {
        $j(this).parent().addClass('gfield_active');
      } else {
        $j(this).parents('.gfield').addClass('gfield_active');
      }
    });

    $j(this).on('blur', function () {
      if ($j(this).val().length == 0) {
        if ($j(this).parent('span').length) {
          $j(this).parent('span').removeClass('gfield_active');
        } else {
          $j(this).parents('.gfield').removeClass('gfield_active');
        }
      }
    });

    if ($j(this).val() != '') {
      if ($j(this).parent('span').length) {
        $j(this).parent().addClass('gfield_active');
      } else {
        $j(this).parents('.gfield').addClass('gfield_active');
      }
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
      video = currentSlide.find('video').get(0);

      if (video != null) {
        if (control === 'play') {
          video.play();
        } else {
          video.pause();
        }
      }
    }
  }

  if (!ClipboardJS.isSupported()) {
    alert("Copy feature not supported in your browser.");
    return;
  }

  var clipboard = new ClipboardJS('.copy-link');

  clipboard.on('success', function (e) {
    const tooltip = e.trigger.nextElementSibling;
    if (tooltip && tooltip.classList.contains('copy-tooltip')) {
      tooltip.style.display = 'block';
      setTimeout(() => {
        tooltip.style.display = 'none';
      }, 2000);
    }
    e.clearSelection();
  });

  clipboard.on('error', function (e) {
    alert("Failed to copy the link.");
    console.error('Error:', e);
  });


  $j('body').on('click', 'a', function (e) {
    let link = $j(this);
    let url = link.attr('href');

    // Allow mailto and tel links to work
    if (url && (url.startsWith('mailto:') || url.startsWith('tel:'))) {
      return;
    }

    e.preventDefault();

    if (link && link.attr('class') && link.attr('class').indexOf("elementor-icon") != -1 && link.attr('href').startsWith('#')) {
      return;
    }

    let isJumpNavigation = link.closest('.cmma-footer-jump-navigation-wrapper')
    if (isJumpNavigation.length) {
      return
    }
    let target = link.attr('target')
    let modal = link.attr('data-modal-id')
    if (!url || modal || url == 'javascript:void(0);') {
      return
    }

    let currentSiteURL = window.location.origin;
    if (!/^https?:\/\//i.test(url)) {
      url = currentSiteURL + url;
    }

    // Check if the URL is valid by checking if it contains "http://" or "https://"
    if (url.startsWith("http://") || url.startsWith("https://") || modal) {

      // Create a new URL object to extract the domain
      let clickedDomain = new URL(url).hostname;
      let currentDomain = window.location.hostname;

      // Check if the domain is the same as the current site's domain
      if (clickedDomain === currentDomain && url.includes("/people/") || url.includes("/member/")) {
        let modalId = link.attr('data-member-modal-id')
        let modalExist = $j(`#cmma-modal-${modalId}`);

        if (modalExist.length) {
          modalExist.addClass('cmma-modal-show');
          history.pushState(null, null, url);
          return initializePeopleSliderWrapper(modalExist);
        } else {
          var ajaxurl = window.location.origin + '/wp-admin/admin-ajax.php';
          try {
            return $j.post(ajaxurl, {
              url,
              'action': 'cmma_create_member_modal'
            }, function (response) {
              if (response && response.data) {
                const { success, html, modal_id, modal_title } = response.data;
                if (success) {
                  link.attr('data-member-modal-id', modal_id)
                  document.title = modal_title + ' - CMMA';
                  let modalExist = $j(`#cmma-modal-${modal_id}`);
                  if (modalExist.length) {
                    modalExist.addClass('cmma-modal-show');
                  } else {
                    $j('body').append(html);
                    initializePeopleSliderWrapper($j(`#cmma-modal-${modal_id}`));
                  }
                  // Custom scrollbar initialization

                  history.pushState(null, null, url);
                  $j(".cmma-people-modal .cmma-modal-content").mCustomScrollbar({
                    scrollButtons: { enable: true },
                    theme: "light-thick",
                    scrollbarPosition: "outside"
                  });
                } else {
                  return _cmmaOpenNewPage(url, target);
                }
              }
            });
          } catch (error) {
            return _cmmaOpenNewPage(url, target);
          }
        }
      } else {
        return _cmmaOpenNewPage(url, target);
      }
    }
    return _cmmaOpenNewPage(url, target);
  });



  initializePeopleSliderWrapper($j('body'));
  function initializePeopleSliderWrapper(dom) {
    var $carousel = $j(dom).find('.cmma-people-slider-wrapper').not('.slick-initialized');
    if ($carousel.length) {
      $carousel.slick();
    }
  }

  function _cmmaOpenNewPage(url, target) {
    console.log(url, target);

    if (target) {
      return window.open(url, target);
    }
    return window.location.href = url;
  }


  $j(document).ready(function ($) {
    // Move CAPTCHA to the end of the form after submit button
    $j('.gform_wrapper').each(function () {
      var $form = $j(this);
      var $captcha = $form.find('.ginput_recaptcha');
      var $submitButton = $form.find('input[type="submit"], button[type="submit"]');

      if ($captcha.length && $submitButton.length) {
        // Move the CAPTCHA after the submit button
        $captcha.insertAfter($submitButton);
      }
    });

    $j('.cmma-gravity-form-button').on('click', function () {
      $j(this).hide();
      $j('.cmma-gravity-form').show();
    });

    $j('.cmma-gravity-form-button').on('click', function () {
      // Set focus on the email input field
      $j('.cmma-gravity-form form input[type="email"]').focus();
    });
  });

  $j('.cky-btn.cky-btn-accept').on('click', function () {
    $j('html, body').animate({ scrollTop: 0 }, 1000); // 1000 ms = 1 second
  });


  $j('body').on('click', '#accept-cookies', function (e) {
    storeCookie(`wp-settings-cookie-accepted`, 'true', 30);
    $j('#custom-cookie-banner').addClass('hide-bar');
    $j('html, body').animate({ scrollTop: 0 }, 1000); // 1000 ms = 1 second
  });

  function storeCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }
});

document.querySelectorAll('.nav-menu-item-link').forEach(link => {
  link.removeEventListener('click', this.onLinkClick);
});



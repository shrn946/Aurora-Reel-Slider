(function ($) {
	'use strict';

	function updateSoundButton(slider, enabled) {
		var mutedSvg = '<svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14 5.23v13.54c0 .71-.86 1.07-1.36.57L8.31 15H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h3.31l4.33-4.34c.5-.5 1.36-.14 1.36.57zM18.71 8.29a1 1 0 0 1 0 1.42L17.41 11l1.3 1.29a1 1 0 1 1-1.42 1.42L16 12.41l-1.29 1.3a1 1 0 0 1-1.42-1.42l1.3-1.29-1.3-1.29a1 1 0 1 1 1.42-1.42L16 9.59l1.29-1.3a1 1 0 0 1 1.42 0z"/></svg>';
		var unmutedSvg = '<svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14 5.23v13.54c0 .71-.86 1.07-1.36.57L8.31 15H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h3.31l4.33-4.34c.5-.5 1.36-.14 1.36.57zM17.5 8.5a1 1 0 0 1 1.41 0A5 5 0 0 1 20.5 12a5 5 0 0 1-1.59 3.5 1 1 0 1 1-1.41-1.42A3 3 0 0 0 18.5 12a3 3 0 0 0-1-2.08 1 1 0 0 1 0-1.42z"/></svg>';
		var activeSlide = slider.querySelector('.swiper-slide-active');
		var button = activeSlide ? activeSlide.querySelector('.evhs-sound-toggle') : null;

		if (!button) {
			return;
		}

		var icon = button.querySelector('.evhs-sound-icon');
		button.setAttribute('data-muted', enabled ? 'false' : 'true');
		button.setAttribute('aria-label', enabled ? 'Mute sound' : 'Enable sound');
		if (icon) {
			icon.innerHTML = enabled ? unmutedSvg : mutedSvg;
		}
	}

	function updateVideos(swiper, slider) {
		if (!swiper || !swiper.slides) {
			return;
		}

		swiper.slides.forEach(function (slide, index) {
			var video = slide.querySelector('video');

			if (!video) {
				return;
			}

			var isActive = index === swiper.activeIndex;
			var soundEnabled = !!slider.evhsSoundEnabled;

			if (isActive) {
				video.muted = !soundEnabled;
				var playVideo = function () {
					var promise;

					try {
						if (video.paused) {
							promise = video.play();
							if (promise && typeof promise.catch === 'function') {
								promise.catch(function () {});
							}
						}
					} catch (e) {}

					video.onended = function () {
						video.currentTime = 0;
						video.play().catch(function () {});
					};
				};

				if (video.readyState >= 3) {
					setTimeout(playVideo, 250);
				} else {
					video.addEventListener('canplay', function handler() {
						video.removeEventListener('canplay', handler);
						setTimeout(playVideo, 250);
					});
				}
			} else {
				video.muted = true;
				video.onended = null;
				try {
					if (!video.paused) {
						video.pause();
					}
				} catch (e) {}
			}
		});

		updateSoundButton(slider, !!slider.evhsSoundEnabled);
	}

	function initVideoSlider($scope) {
		var $sliders = $scope.find('.evhs-video-slider');

		$sliders.each(function () {
			var slider = this;
			slider.evhsSoundEnabled = false;

			if (slider.evhsSwiper) {
				slider.evhsSwiper.destroy(true, true);
			}

			var settings = slider.dataset || {};
			var speed = parseInt(settings.speed || 1000, 10);
			var spaceDesktop = parseInt(settings.spaceDesktop || 90, 10);
			var spaceTablet = parseInt(settings.spaceTablet || 50, 10);
			var spaceMobile = parseInt(settings.spaceMobile || 24, 10);
			var autoplay = settings.autoplay === 'yes';

			var swiper = new Swiper(slider, {
				slidesPerView: 'auto',
				centeredSlides: true,
				spaceBetween: spaceMobile,
				speed: speed,
				allowTouchMove: true,
				loop: false,
				autoplay: autoplay ? {
					delay: parseInt(settings.autoplayDelay || 3500, 10),
					disableOnInteraction: false
				} : false,
				breakpoints: {
					768: { spaceBetween: spaceTablet },
					1025: { spaceBetween: spaceDesktop }
				},
				on: {
					init: function () {
						updateVideos(this, slider);
						var wrapper = slider.closest('.evhs-wrapper');
						if (wrapper) {
							wrapper.classList.remove('evhs-loading');
						}
					},
					slideChange: function () { updateVideos(this, slider); }
				}
			});

			slider.evhsSwiper = swiper;
			slider.addEventListener('click', function (event) {
				var button = event.target.closest('.evhs-sound-toggle');
				if (!button) {
					return;
				}

				event.preventDefault();
				event.stopPropagation();

				slider.evhsSoundEnabled = !slider.evhsSoundEnabled;
				var activeSlide = slider.querySelector('.swiper-slide-active');
				var activeVideo = activeSlide ? activeSlide.querySelector('video') : null;
				if (activeVideo) {
					activeVideo.muted = !slider.evhsSoundEnabled;
					if (slider.evhsSoundEnabled) {
						try {
							var playPromise = activeVideo.play();
							if (playPromise && typeof playPromise.catch === 'function') {
								playPromise.catch(function () {});
							}
						} catch (e) {}
					}
				}
				updateVideos(swiper, slider);
			});

			var isScrolling = false;
			slider.addEventListener('wheel', function (event) {
				var isHorizontalScroll = Math.abs(event.deltaX) > Math.abs(event.deltaY);

				if (!isHorizontalScroll) {
					return;
				}

				event.preventDefault();

				if (isScrolling || Math.abs(event.deltaX) < 30) {
					return;
				}

				isScrolling = true;

				if (event.deltaX > 0) {
					if (swiper.activeIndex === swiper.slides.length - 1) {
						swiper.slideTo(0);
					} else {
						swiper.slideNext();
					}
				} else {
					if (swiper.activeIndex === 0) {
						swiper.slideTo(swiper.slides.length - 1);
					} else {
						swiper.slidePrev();
					}
				}

				setTimeout(function () {
					isScrolling = false;
				}, speed + 100);
			}, { passive: false });
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/evhs-video-highlight-slider.default', initVideoSlider);
	});
})(jQuery);

(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var galleryItems = document.querySelectorAll('.gallery__item');
  var charStaggerSelectors = [
    '.hero__name',
    '.hero__intro-title',
    '.about__heading',
    '.timeline__heading',
    '.comment__heading',
    '.artworks__heading',
    '.closing__heading'
  ];
  var textSelectors = [
    '.hero__intro-text',
    '.hero__meta-date',
    '.about__text p',
    '.timeline__intro',
    '.timeline__range',
    '.timeline__title',
    '.timeline__text',
    '.comment__text',
    '.comment__name',
    '.closing__text p',
    '.closing__contact-label',
    '.closing__contact p',
    '.closing__meta-date'
  ];
  var textElements = [];
  var charStaggerElements = [];

  charStaggerSelectors.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      charStaggerElements.push(el);
    });
  });

  textSelectors.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      textElements.push(el);
    });
  });

  function splitCharStagger(el) {
    if (el.classList.contains('char-stagger') || el.querySelector('.char-stagger__track')) {
      return;
    }

    var text = el.textContent.trim();
    el.textContent = '';
    el.classList.add('char-stagger');
    el.setAttribute('aria-label', text);

    var track = document.createElement('span');
    track.className = 'char-stagger__track';
    track.setAttribute('aria-hidden', 'true');

    var words = text.split(/\s+/).filter(Boolean);
    var charIndex = 0;

    words.forEach(function (word, wordIndex) {
      var wordWrap = document.createElement('span');
      wordWrap.className = 'char-stagger__word';

      word.split('').forEach(function (char) {
        var span = document.createElement('span');
        span.className = 'char-stagger__char';
        span.textContent = char;
        span.style.setProperty('--char-index', charIndex);
        charIndex += 1;
        wordWrap.appendChild(span);
      });

      track.appendChild(wordWrap);

      if (wordIndex < words.length - 1) {
        var space = document.createElement('span');
        space.className = 'char-stagger__space';
        space.innerHTML = '\u00a0';
        space.setAttribute('aria-hidden', 'true');
        track.appendChild(space);
      }
    });

    el.appendChild(track);

    if (el.classList.contains('hero__name')) {
      el.classList.add('char-stagger--write');
    }
  }

  function wrapTextMask(el) {
    if (el.classList.contains('text-mask') || el.querySelector('.text-mask__inner')) {
      return;
    }

    var inner = document.createElement('span');
    inner.className = 'text-mask__inner';

    while (el.firstChild) {
      inner.appendChild(el.firstChild);
    }

    el.classList.add('text-mask');
    el.appendChild(inner);
  }

  function showReveal(el) {
    if (el.classList.contains('is-visible')) {
      return;
    }

    el.classList.remove('is-visible');
    el.style.setProperty('--reveal-delay', el.dataset.revealDelay || '0s');
    void el.offsetWidth;
    el.classList.add('is-visible');
  }

  function hideReveal(el) {
    if (!el.classList.contains('is-visible')) {
      return;
    }

    el.classList.remove('is-visible');
  }

  function isInView(el) {
    var rect = el.getBoundingClientRect();
    var viewHeight = window.innerHeight;
    var viewWidth = window.innerWidth;
    return (
      rect.top < viewHeight * 0.92 &&
      rect.bottom > viewHeight * 0.08 &&
      rect.left < viewWidth * 0.95 &&
      rect.right > viewWidth * 0.05
    );
  }

  function updateRevealVisibility(el) {
    if (isInView(el)) {
      showReveal(el);
    } else {
      hideReveal(el);
    }
  }

  var revealTargets = [];

  galleryItems.forEach(function (item, index) {
    var img = item.querySelector('img');
    if (img) {
      img.setAttribute('data-parallax', '0.06');
    }

    item.dataset.revealDelay = (index % 4) * 0.3 + 's';
    revealTargets.push(item);
  });

  charStaggerElements.forEach(function (el) {
    splitCharStagger(el);
    revealTargets.push(el);
  });

  textElements.forEach(function (el, index) {
    wrapTextMask(el);
    el.dataset.revealDelay = (index % 3) * 0.14 + 's';
    revealTargets.push(el);
  });

  if (reduceMotion) {
    revealTargets.forEach(function (el) {
      el.classList.add('is-visible');
    });
  } else if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            showReveal(entry.target);
          } else {
            hideReveal(entry.target);
          }
        });
      },
      { threshold: [0, 0.1, 0.2], rootMargin: '0px' }
    );

    revealTargets.forEach(function (el) {
      revealObserver.observe(el);
    });
  } else {
    revealTargets.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  var revealTicking = false;

  function onRevealScroll() {
    if (reduceMotion || !revealTargets.length) {
      return;
    }

    if (!revealTicking) {
      revealTicking = true;
      window.requestAnimationFrame(function () {
        revealTargets.forEach(updateRevealVisibility);
        revealTicking = false;
      });
    }
  }

  window.addEventListener('scroll', onRevealScroll, { passive: true });
  window.addEventListener('resize', onRevealScroll, { passive: true });
  onRevealScroll();

  function initLightbox() {
    var galleryItems = document.querySelectorAll('.gallery__item');

    if (!galleryItems.length) {
      return;
    }

    var overlay = document.createElement('div');
    overlay.className = 'lightbox';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML =
      '<div class="lightbox__backdrop" data-lightbox-close></div>' +
      '<div class="lightbox__dialog" role="dialog" aria-modal="true" aria-label="Artwork preview">' +
      '<button type="button" class="lightbox__close" aria-label="Close">&times;</button>' +
      '<button type="button" class="lightbox__nav lightbox__nav--prev" aria-label="Previous artwork">&lsaquo;</button>' +
      '<button type="button" class="lightbox__nav lightbox__nav--next" aria-label="Next artwork">&rsaquo;</button>' +
      '<figure class="lightbox__figure">' +
      '<div class="lightbox__stage">' +
      '<img class="lightbox__img" src="" alt="">' +
      '<video class="lightbox__video" playsinline controls hidden></video>' +
      '<div class="lightbox__chrome">' +
      '<figcaption class="lightbox__caption"></figcaption>' +
      '<div class="lightbox__dots" aria-hidden="true"></div>' +
      '<div class="lightbox__progress" aria-hidden="true"><span class="lightbox__progress-bar"></span></div>' +
      '</div>' +
      '</div>' +
      '</figure>' +
      '<p class="lightbox__hint">Swipe left or right</p>' +
      '</div>';
    document.body.appendChild(overlay);

    var lightboxImg = overlay.querySelector('.lightbox__img');
    var lightboxVideo = overlay.querySelector('.lightbox__video');
    var lightboxCaption = overlay.querySelector('.lightbox__caption');
    var prevBtn = overlay.querySelector('.lightbox__nav--prev');
    var nextBtn = overlay.querySelector('.lightbox__nav--next');
    var hintEl = overlay.querySelector('.lightbox__hint');
    var dotsEl = overlay.querySelector('.lightbox__dots');
    var progressEl = overlay.querySelector('.lightbox__progress');
    var activeSlides = [];
    var currentIndex = 0;
    var touchStartX = 0;
    var touchStartY = 0;
    var swipeThreshold = 48;
    var autoplayDelay = 4000;
    var autoplayTimer = null;
    var autoplayActive = false;
    var hintTimer = null;
    var hintDismissed = false;
    var isClosing = false;
    var isAnimating = false;
    var fadeMs = reduceMotion ? 0 : 320;

    function getGallerySlides(item) {
      var gallery = item.closest('.gallery');

      if (!gallery) {
        return [];
      }

      return Array.prototype.slice.call(gallery.querySelectorAll('.gallery__item')).map(function (el) {
        var thumb = el.querySelector('img');
        var type = el.getAttribute('data-media-type') || 'image';

        return {
          type: type,
          src:
            type === 'video'
              ? el.getAttribute('data-media-src') || ''
              : thumb
                ? thumb.currentSrc || thumb.src
                : '',
          poster: thumb ? thumb.currentSrc || thumb.src : '',
          alt: thumb ? thumb.alt || '' : ''
        };
      });
    }

    function stopActiveVideo() {
      if (!lightboxVideo) {
        return;
      }

      lightboxVideo.onended = null;
      lightboxVideo.pause();
      lightboxVideo.removeAttribute('src');
      lightboxVideo.load();
      lightboxVideo.hidden = true;
    }

    function playActiveVideo() {
      if (!lightboxVideo || lightboxVideo.hidden) {
        return;
      }

      var playPromise = lightboxVideo.play();

      if (playPromise && typeof playPromise.catch === 'function') {
        playPromise.catch(function () {
          lightboxVideo.muted = true;
          lightboxVideo.play().catch(function () {});
        });
      }
    }

    function setCaption() {
      lightboxCaption.textContent = '';
      lightboxCaption.hidden = true;
    }

    function updateSlideMeta(index) {
      currentIndex = index;
      prevBtn.disabled = index === 0;
      nextBtn.disabled = index === activeSlides.length - 1;
      buildDots();

      if (activeSlides[index] && activeSlides[index].type === 'video') {
        progressEl.hidden = true;
        progressEl.classList.remove('is-running');
      } else {
        resetProgressBar();
      }
    }

    function clearFadeClasses() {
      lightboxImg.classList.remove('is-fade-out', 'is-fade-in');
      lightboxCaption.classList.remove('is-fade-out', 'is-fade-in');
    }

    function fadeInImage() {
      if (fadeMs === 0) {
        return;
      }

      lightboxImg.classList.add('is-fade-in');

      window.requestAnimationFrame(function () {
        window.requestAnimationFrame(function () {
          lightboxImg.classList.remove('is-fade-in');
        });
      });
    }

    function applySlide(index) {
      var slide = activeSlides[index];

      stopActiveVideo();

      if (!slide) {
        return;
      }

      if (slide.type === 'video') {
        lightboxImg.hidden = true;
        lightboxImg.removeAttribute('src');
        lightboxVideo.hidden = false;
        lightboxVideo.poster = slide.poster;
        lightboxVideo.src = slide.src;
        lightboxVideo.load();
        playActiveVideo();
      } else {
        lightboxVideo.hidden = true;
        lightboxImg.hidden = false;
        lightboxImg.src = slide.src;
        lightboxImg.alt = slide.alt;
      }

      updateSlideMeta(index);
      setCaption();
    }

    function fadeToSlide(index) {
      if (isAnimating) {
        return;
      }

      if (fadeMs === 0) {
        applySlide(index);
        scheduleAutoplay();
        return;
      }

      isAnimating = true;
      lightboxImg.classList.add('is-fade-out');
      lightboxCaption.classList.add('is-fade-out');

      window.setTimeout(function () {
        applySlide(index);
        lightboxImg.classList.remove('is-fade-out');
        lightboxCaption.classList.remove('is-fade-out');
        lightboxImg.classList.add('is-fade-in');
        lightboxCaption.classList.add('is-fade-in');

        window.requestAnimationFrame(function () {
          window.requestAnimationFrame(function () {
            lightboxImg.classList.remove('is-fade-in');
            lightboxCaption.classList.remove('is-fade-in');
            isAnimating = false;
            scheduleAutoplay();
          });
        });
      }, fadeMs);
    }

    function hideHint() {
      if (hintDismissed) {
        return;
      }

      hintDismissed = true;
      overlay.classList.remove('is-hinting');
      hintEl.classList.add('is-hidden');

      if (hintTimer) {
        window.clearTimeout(hintTimer);
        hintTimer = null;
      }
    }

    function showHint() {
      hintDismissed = false;
      overlay.classList.add('is-hinting');
      hintEl.classList.remove('is-hidden');

      if (hintTimer) {
        window.clearTimeout(hintTimer);
      }

      hintTimer = window.setTimeout(hideHint, 4500);
    }

    function buildDots() {
      dotsEl.innerHTML = '';
      dotsEl.hidden = activeSlides.length < 2;

      activeSlides.forEach(function (_, dotIndex) {
        var dot = document.createElement('span');
        dot.className = 'lightbox__dot';

        if (dotIndex === currentIndex) {
          dot.classList.add('is-active');
        }

        dotsEl.appendChild(dot);
      });
    }

    function resetProgressBar() {
      if (!progressEl || activeSlides.length < 2) {
        progressEl.hidden = true;
        return;
      }

      if (activeSlides[currentIndex] && activeSlides[currentIndex].type === 'video') {
        progressEl.hidden = true;
        progressEl.classList.remove('is-running');
        return;
      }

      progressEl.hidden = false;
      progressEl.style.setProperty('--autoplay-duration', autoplayDelay + 'ms');
      progressEl.classList.remove('is-running');
      void progressEl.offsetWidth;
      progressEl.classList.add('is-running');
    }

    function stopAutoplay() {
      autoplayActive = false;

      if (autoplayTimer) {
        window.clearTimeout(autoplayTimer);
        autoplayTimer = null;
      }

      stopActiveVideo();
    }

    function scheduleAutoplay() {
      if (autoplayTimer) {
        window.clearTimeout(autoplayTimer);
        autoplayTimer = null;
      }

      if (!autoplayActive || reduceMotion || activeSlides.length < 2) {
        return;
      }

      if (!overlay.classList.contains('is-open') || isClosing || isAnimating) {
        return;
      }

      var slide = activeSlides[currentIndex];

      if (slide && slide.type === 'video' && lightboxVideo && !lightboxVideo.hidden) {
        lightboxVideo.onended = function () {
          lightboxVideo.onended = null;

          if (!overlay.classList.contains('is-open') || isClosing) {
            return;
          }

          var nextIndex = currentIndex < activeSlides.length - 1 ? currentIndex + 1 : 0;
          renderAt(nextIndex, 1);
        };

        return;
      }

      autoplayTimer = window.setTimeout(function () {
        autoplayTimer = null;

        if (!overlay.classList.contains('is-open') || isClosing || isAnimating) {
          scheduleAutoplay();
          return;
        }

        var nextIndex = currentIndex < activeSlides.length - 1 ? currentIndex + 1 : 0;
        renderAt(nextIndex, 1);
      }, autoplayDelay);
    }

    function startAutoplay() {
      if (reduceMotion || activeSlides.length < 2) {
        return;
      }

      autoplayActive = true;
      scheduleAutoplay();
    }

    function renderAt(index, direction) {
      direction = direction || 0;

      if (direction === 0) {
        clearFadeClasses();
        applySlide(index);
        fadeInImage();
        scheduleAutoplay();
        return;
      }

      fadeToSlide(index);
    }

    function openAt(item) {
      var gallery = item.closest('.gallery');

      activeSlides = getGallerySlides(item);
      currentIndex = gallery
        ? Math.max(0, Array.prototype.indexOf.call(gallery.querySelectorAll('.gallery__item'), item))
        : 0;
      isClosing = false;
      overlay.classList.add('is-open', 'is-entering');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.classList.add('lightbox-open');
      renderAt(currentIndex, 0);
      showHint();
      startAutoplay();

      window.setTimeout(function () {
        overlay.classList.remove('is-entering');
      }, 820);
    }

    function closeLightbox() {
      if (isClosing) {
        return;
      }

      stopAutoplay();
      hideHint();
      isClosing = true;
      overlay.classList.add('is-leaving');

      window.setTimeout(function () {
        overlay.classList.remove('is-open', 'is-leaving', 'is-entering', 'is-hinting');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lightbox-open');
        lightboxImg.hidden = false;
        lightboxImg.removeAttribute('src');
        stopActiveVideo();
        clearFadeClasses();
        lightboxCaption.textContent = '';
        activeSlides = [];
        currentIndex = 0;
        isAnimating = false;
        isClosing = false;
      }, reduceMotion ? 0 : 550);
    }

    function showPrevious() {
      if (currentIndex > 0 && !isAnimating && !isClosing) {
        hideHint();
        renderAt(currentIndex - 1, -1);
      }
    }

    function showNext() {
      if (currentIndex < activeSlides.length - 1 && !isAnimating && !isClosing) {
        hideHint();
        renderAt(currentIndex + 1, 1);
      }
    }

    galleryItems.forEach(function (item) {
      var img = item.querySelector('img');

      if (!img) {
        return;
      }

      var isVideo = item.getAttribute('data-media-type') === 'video';

      img.setAttribute('role', 'button');
      img.setAttribute(
        'aria-label',
        (img.alt || (isVideo ? 'View video' : 'View artwork')) + ' — tap to enlarge'
      );

      img.addEventListener('click', function () {
        openAt(item);
      });

      img.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openAt(item);
        }
      });
    });

    prevBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      showPrevious();
    });

    nextBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      showNext();
    });

    overlay.querySelector('[data-lightbox-close]').addEventListener('click', closeLightbox);
    overlay.querySelector('.lightbox__close').addEventListener('click', closeLightbox);

    overlay.addEventListener(
      'touchstart',
      function (e) {
        if (!overlay.classList.contains('is-open')) {
          return;
        }

        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
      },
      { passive: true }
    );

    overlay.addEventListener(
      'touchend',
      function (e) {
        if (!overlay.classList.contains('is-open')) {
          return;
        }

        var touchEndX = e.changedTouches[0].screenX;
        var touchEndY = e.changedTouches[0].screenY;
        var deltaX = touchEndX - touchStartX;
        var deltaY = touchEndY - touchStartY;

        if (Math.abs(deltaX) < swipeThreshold || Math.abs(deltaX) < Math.abs(deltaY)) {
          return;
        }

        if (deltaX < 0) {
          hideHint();
          showNext();
        } else {
          hideHint();
          showPrevious();
        }
      },
      { passive: true }
    );

    document.addEventListener('keydown', function (e) {
      if (!overlay.classList.contains('is-open')) {
        return;
      }

      if (e.key === 'Escape') {
        closeLightbox();
      } else if (e.key === 'ArrowLeft') {
        showPrevious();
      } else if (e.key === 'ArrowRight') {
        showNext();
      }
    });
  }

  initLightbox();
  initGallerySnap();

  function initGallerySnap() {
    var mobileQuery = window.matchMedia('(max-width: 768px)');
    var galleries = document.querySelectorAll('.gallery:not(.gallery--closing)');

    if (!galleries.length) {
      return;
    }

    galleries.forEach(function (gallery) {
      var items = Array.prototype.slice.call(gallery.querySelectorAll('.gallery__item'));

      if (items.length < 2) {
        return;
      }

      var shell = document.createElement('div');
      shell.className = 'gallery-shell';
      gallery.parentNode.insertBefore(shell, gallery);
      shell.appendChild(gallery);

      var pager = document.createElement('div');
      pager.className = 'gallery__pager';
      pager.setAttribute('aria-hidden', 'true');

      items.forEach(function (_, index) {
        var dot = document.createElement('span');
        dot.className = 'gallery__pager-dot';
        if (index === 0) {
          dot.classList.add('is-active');
        }
        pager.appendChild(dot);
      });

      shell.appendChild(pager);

      var dots = Array.prototype.slice.call(pager.querySelectorAll('.gallery__pager-dot'));
      var touchStartX = 0;
      var touchStartY = 0;
      var touchStartTime = 0;
      var isDragging = false;
      var snapTimer = null;

      function updateChrome() {
        if (!mobileQuery.matches) {
          return;
        }

        var index = getNearestIndex();

        dots.forEach(function (dot, dotIndex) {
          dot.classList.toggle('is-active', dotIndex === index);
        });

        shell.classList.toggle('is-at-start', index === 0);
        shell.classList.toggle('is-at-end', index === items.length - 1);
      }

      function getNearestIndex() {
        var center = gallery.scrollLeft + gallery.clientWidth * 0.5;
        var nearest = 0;
        var minDistance = Infinity;

        items.forEach(function (item, index) {
          var itemCenter = item.offsetLeft + item.offsetWidth * 0.5;
          var distance = Math.abs(center - itemCenter);

          if (distance < minDistance) {
            minDistance = distance;
            nearest = index;
          }
        });

        return nearest;
      }

      function scrollToIndex(index, behavior) {
        var item = items[index];

        if (!item) {
          return;
        }

        var targetLeft = item.offsetLeft + item.offsetWidth * 0.5 - gallery.clientWidth * 0.5;

        gallery.scrollTo({
          left: Math.max(0, targetLeft),
          behavior: behavior || 'smooth'
        });

        window.requestAnimationFrame(updateChrome);
      }

      function snapToNearest(behavior) {
        scrollToIndex(getNearestIndex(), behavior);
      }

      updateChrome();

      gallery.addEventListener(
        'touchstart',
        function (e) {
          if (!mobileQuery.matches || e.touches.length !== 1) {
            return;
          }

          touchStartX = e.touches[0].clientX;
          touchStartY = e.touches[0].clientY;
          touchStartTime = Date.now();
          isDragging = true;

          if (snapTimer) {
            window.clearTimeout(snapTimer);
            snapTimer = null;
          }
        },
        { passive: true }
      );

      gallery.addEventListener(
        'touchmove',
        function (e) {
          if (!isDragging || !mobileQuery.matches) {
            return;
          }

          var deltaX = Math.abs(e.touches[0].clientX - touchStartX);
          var deltaY = Math.abs(e.touches[0].clientY - touchStartY);

          if (deltaX > deltaY && deltaX > 8) {
            e.preventDefault();
          }
        },
        { passive: false }
      );

      gallery.addEventListener(
        'touchend',
        function (e) {
          if (!mobileQuery.matches || !isDragging) {
            return;
          }

          isDragging = false;

          var touchEndX = e.changedTouches[0].clientX;
          var touchEndY = e.changedTouches[0].clientY;
          var deltaX = touchStartX - touchEndX;
          var deltaY = touchStartY - touchEndY;
          var elapsed = Date.now() - touchStartTime;
          var currentIndex = getNearestIndex();

          if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 36) {
            var targetIndex = currentIndex;

            if (deltaX > 0 && currentIndex < items.length - 1) {
              targetIndex = currentIndex + 1;
            } else if (deltaX < 0 && currentIndex > 0) {
              targetIndex = currentIndex - 1;
            }

            scrollToIndex(targetIndex, 'smooth');
            return;
          }

          if (elapsed < 280 && Math.abs(deltaX) > 18) {
            var flickIndex = currentIndex;

            if (deltaX > 0 && currentIndex < items.length - 1) {
              flickIndex = currentIndex + 1;
            } else if (deltaX < 0 && currentIndex > 0) {
              flickIndex = currentIndex - 1;
            }

            scrollToIndex(flickIndex, 'smooth');
            return;
          }

          snapToNearest('smooth');
        },
        { passive: true }
      );

      gallery.addEventListener(
        'scroll',
        function () {
          if (!mobileQuery.matches) {
            return;
          }

          if (snapTimer) {
            window.clearTimeout(snapTimer);
          }

          snapTimer = window.setTimeout(function () {
            snapToNearest('smooth');
            updateChrome();
            snapTimer = null;
          }, 140);
        },
        { passive: true }
      );

      gallery.addEventListener('scroll', updateChrome, { passive: true });
      window.addEventListener('resize', updateChrome, { passive: true });
    });
  }

  function initClosingGallery() {
    var root = document.querySelector('[data-closing-gallery]');

    if (!root) {
      return;
    }

    var items = Array.prototype.slice.call(
      root.querySelectorAll('.gallery--closing .gallery__item')
    );

    if (items.length < 2) {
      return;
    }

    var prevBtn = root.querySelector('.closing__gallery-nav--prev');
    var nextBtn = root.querySelector('.closing__gallery-nav--next');
    var index = 0;

    function showItem(nextIndex) {
      index = Math.max(0, Math.min(items.length - 1, nextIndex));
      items.forEach(function (item, itemIndex) {
        item.classList.toggle('is-active', itemIndex === index);
      });

      if (prevBtn) {
        prevBtn.disabled = index === 0;
      }

      if (nextBtn) {
        nextBtn.disabled = index === items.length - 1;
      }
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        showItem(index - 1);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        showItem(index + 1);
      });
    }

    showItem(0);
  }

  initClosingGallery();

  var parallaxImages = document.querySelectorAll('[data-parallax]');
  if (!parallaxImages.length) {
    return;
  }

  var parallaxTicking = false;

  function updateParallax() {
    var viewHeight = window.innerHeight;
    var viewCenter = viewHeight * 0.5;

    parallaxImages.forEach(function (img) {
      var rect = img.getBoundingClientRect();
      var elementCenter = rect.top + rect.height * 0.5;
      var delta = (elementCenter - viewCenter) / viewHeight;
      var strength = parseFloat(img.getAttribute('data-parallax')) || 0.08;
      var offset = delta * strength * 72;

      img.style.transform = 'translate3d(0, ' + offset.toFixed(2) + 'px, 0)';
    });

    parallaxTicking = false;
  }

  function onParallaxScroll() {
    if (!parallaxTicking) {
      parallaxTicking = true;
      window.requestAnimationFrame(updateParallax);
    }
  }

  window.addEventListener('scroll', onParallaxScroll, { passive: true });
  window.addEventListener('resize', onParallaxScroll, { passive: true });
  updateParallax();
})();

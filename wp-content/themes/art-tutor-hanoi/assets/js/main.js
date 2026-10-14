(function () {
  var toggle = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.nav-wrapper');
  var overlay = document.querySelector('.nav-overlay');

  if (toggle && nav && overlay) {
    function setOpen(open) {
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      nav.classList.toggle('is-open', open);
      overlay.classList.toggle('is-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
    }

    toggle.addEventListener('click', function () {
      setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    overlay.addEventListener('click', function () {
      setOpen(false);
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        setOpen(false);
      });
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) setOpen(false);
    });
  }

  (function initDesktopNavDropdowns() {
    var navItems = document.querySelectorAll('.nav-item');

    if (!navItems.length) {
      return;
    }

    function isDesktopNav() {
      return window.matchMedia('(min-width: 769px)').matches;
    }

    function setItemOpen(item, open) {
      item.classList.toggle('is-open', open);
      var trigger = item.querySelector('.nav-item__trigger');
      if (trigger) {
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (!open) {
          trigger.blur();
        }
      }
    }

    function closeAll(except) {
      navItems.forEach(function (item) {
        if (item !== except) {
          setItemOpen(item, false);
        }
      });
    }

    navItems.forEach(function (item) {
      var trigger = item.querySelector('.nav-item__trigger');
      var submenu = item.querySelector('.nav-submenu');

      if (!trigger || !submenu) {
        return;
      }

      trigger.setAttribute('aria-haspopup', 'true');
      trigger.setAttribute('aria-expanded', 'false');

      trigger.addEventListener('click', function (e) {
        if (!isDesktopNav()) {
          return;
        }

        if (!item.classList.contains('is-open')) {
          e.preventDefault();
          closeAll(item);
          setItemOpen(item, true);
        }
      });

      submenu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
          setItemOpen(item, false);
        });
      });
    });

    document.addEventListener('click', function (e) {
      if (!isDesktopNav()) {
        return;
      }

      var insideItem = false;
      navItems.forEach(function (item) {
        if (item.contains(e.target)) {
          insideItem = true;
        }
      });

      if (!insideItem) {
        closeAll(null);
      }
    });

    window.addEventListener('resize', function () {
      if (!isDesktopNav()) {
        closeAll(null);
      }
    });
  })();

  function bindImageLoadState(img) {
    if (img.complete && img.naturalWidth > 0) {
      img.classList.add('is-loaded');
      return;
    }
    img.addEventListener('load', function () {
      img.classList.add('is-loaded');
    });
    img.addEventListener('error', function () {
      img.classList.remove('js-lazy-img');
      img.removeAttribute('src');
      img.style.display = 'none';
      var parent = img.parentNode;
      if (parent && !parent.querySelector('.news-card__image-placeholder')) {
        var placeholder = document.createElement('span');
        placeholder.className = 'news-card__image-placeholder';
        placeholder.setAttribute('aria-hidden', 'true');
        parent.appendChild(placeholder);
      }
    });
  }

  function loadLazyImage(img) {
    var src = img.getAttribute('data-src');
    if (!src || img.dataset.lazyLoaded === '1') {
      return;
    }
    img.dataset.lazyLoaded = '1';
    img.src = src;
    bindImageLoadState(img);
  }

  function initLazyImages(selector, rootMargin) {
    var images = document.querySelectorAll(selector);
    if (!images.length) {
      return function () {};
    }

    rootMargin = rootMargin || '160px 0px';

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            loadLazyImage(entry.target);
            observer.unobserve(entry.target);
          }
        });
      }, { rootMargin: rootMargin, threshold: 0.01 });

      images.forEach(function (img) {
        if (img.getAttribute('data-src')) {
          observer.observe(img);
        }
      });

      return function refreshLazyImages() {
        images.forEach(function (img) {
          if (img.getAttribute('data-src') && img.dataset.lazyLoaded !== '1') {
            observer.observe(img);
          }
        });
      };
    }

    images.forEach(loadLazyImage);
    return function () {};
  }

  var refreshGalleryLazyImages = initLazyImages('.gallery-item img.js-lazy-img');
  var refreshCommunityLazyImages = initLazyImages('.community-carousel img.js-lazy-img');
  initLazyImages('.students-artworks-grid img.js-lazy-img');

  var galleryLightbox = document.getElementById('gallery-lightbox');
  var galleryLightboxImg = galleryLightbox && galleryLightbox.querySelector('.gallery-lightbox__img');
  var galleryLightboxClose = galleryLightbox && galleryLightbox.querySelector('.gallery-lightbox__close');

  function openGalleryLightbox(img) {
    if (!galleryLightbox || !galleryLightboxImg || !img) return;
    galleryLightboxImg.src = img.getAttribute('data-full') || img.src;
    galleryLightboxImg.alt = img.alt || '';
    galleryLightbox.hidden = false;
    galleryLightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (galleryLightboxClose) galleryLightboxClose.focus();
  }

  function closeGalleryLightbox() {
    if (!galleryLightbox || !galleryLightboxImg) return;
    galleryLightbox.hidden = true;
    galleryLightbox.setAttribute('aria-hidden', 'true');
    galleryLightboxImg.removeAttribute('src');
    galleryLightboxImg.alt = '';
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.gallery-item').forEach(function (item) {
    var img = item.querySelector('img');
    if (!img) return;

    item.addEventListener('click', function () {
      openGalleryLightbox(img);
    });

    item.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openGalleryLightbox(img);
      }
    });
  });

  if (galleryLightbox) {
    if (galleryLightboxClose) {
      galleryLightboxClose.addEventListener('click', closeGalleryLightbox);
    }
    galleryLightbox.addEventListener('click', function (e) {
      if (e.target === galleryLightbox) closeGalleryLightbox();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && galleryLightbox && !galleryLightbox.hidden) {
        closeGalleryLightbox();
      }
    });
  }

  var scrollRadiusMin = 12;
  var scrollRadiusRatio = 0.32;
  var scrollRadiusTick = false;
  var scrollRadiusMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  function smoothstep(t) {
    return t * t * (3 - 2 * t);
  }

  function applyScrollRadius(elements, cssVar) {
    if (!elements.length) return;

    var vh = window.innerHeight;
    var centerY = vh * 0.5;
    var falloff = vh * 0.44;

    elements.forEach(function (el) {
      var rect = el.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > vh) {
        el.style.setProperty(cssVar, scrollRadiusMin + 'px');
        return;
      }

      var itemCenterY = rect.top + rect.height * 0.5;
      var dist = Math.abs(itemCenterY - centerY);
      var t = 1 - Math.min(dist / falloff, 1);
      var size = Math.min(rect.width, rect.height);
      var radiusMax = size * scrollRadiusRatio;
      var radius = scrollRadiusMin + (radiusMax - scrollRadiusMin) * smoothstep(t);
      el.style.setProperty(cssVar, radius.toFixed(1) + 'px');
    });
  }

  function updateScrollRadiusEffects() {
    scrollRadiusTick = false;
    if (scrollRadiusMotion.matches) return;

    applyScrollRadius(document.querySelectorAll('.gallery-item'), '--gallery-radius');
    applyScrollRadius(
      document.querySelectorAll('.community-carousel .news-card__image'),
      '--scroll-radius'
    );
    applyScrollRadius(
      document.querySelectorAll('.students-artworks-grid .news-card__image'),
      '--scroll-radius'
    );
  }

  function scheduleScrollRadiusEffects() {
    if (scrollRadiusTick) return;
    scrollRadiusTick = true;
    requestAnimationFrame(updateScrollRadiusEffects);
  }

  if (
    document.querySelector('.gallery-item') ||
    document.querySelector('.community-carousel .news-card__image') ||
    document.querySelector('.students-artworks-grid .news-card__image')
  ) {
    updateScrollRadiusEffects();
    window.addEventListener('scroll', scheduleScrollRadiusEffects, { passive: true });
    window.addEventListener('resize', updateScrollRadiusEffects);
    scrollRadiusMotion.addEventListener('change', updateScrollRadiusEffects);
  }

  var moreBtn = document.querySelector('.gallery-more');
  if (moreBtn) {
    moreBtn.addEventListener('click', function () {
      var hidden = document.querySelector('.gallery-batch:not(.is-visible)');
      if (hidden) {
        hidden.classList.add('is-visible');
        if (!document.querySelector('.gallery-batch:not(.is-visible)')) {
          moreBtn.classList.add('is-hidden');
        }
        scheduleScrollRadiusEffects();
        refreshGalleryLazyImages();
      }
    });
  }

  var track = document.getElementById('testimonials-track');
  var dotsContainer = document.getElementById('testimonials-dots');
  if (track && dotsContainer) {
    var wrap = track.parentElement;
    var slides = track.querySelectorAll('.testimonial-slide');
    var total = slides.length;
    var current = 0;

    function updateTestimonialsSlider() {
      if (!wrap) return;
      var slideWidth = wrap.getBoundingClientRect().width;
      if (slideWidth <= 0) return;

      slides.forEach(function (slide) {
        slide.style.width = slideWidth + 'px';
      });
      track.style.transform = 'translateX(-' + (current * slideWidth) + 'px)';
    }

    function goTo(index) {
      current = (index + total) % total;
      updateTestimonialsSlider();
      dotsContainer.querySelectorAll('.testimonials-dot').forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === current);
        dot.setAttribute('aria-current', i === current ? 'true' : 'false');
      });
    }

    for (var i = 0; i < total; i++) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'testimonials-dot' + (i === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Go to review ' + (i + 1));
      if (i === 0) dot.setAttribute('aria-current', 'true');
      (function (idx) {
        dot.addEventListener('click', function () { goTo(idx); });
      })(i);
      dotsContainer.appendChild(dot);
    }

    var prevBtn = document.querySelector('.testimonials-nav--prev');
    var nextBtn = document.querySelector('.testimonials-nav--next');
    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

    window.addEventListener('resize', updateTestimonialsSlider);
    updateTestimonialsSlider();
  }

  var newsTrack = document.getElementById('news-track');
  var newsWrap = document.getElementById('news-track-wrap');
  if (newsTrack && newsWrap) {
    var newsCards = newsTrack.querySelectorAll('.news-card');
    var newsCurrent = 0;
    var newsPrev = document.querySelector('.news-nav--prev');
    var newsNext = document.querySelector('.news-nav--next');

    function getNewsVisible() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function getNewsGap() {
      return window.innerWidth <= 768 ? 16 : 24;
    }

    function updateNewsSlider() {
      var visible = getNewsVisible();
      var gap = getNewsGap();
      var maxIndex = Math.max(0, newsCards.length - visible);
      if (newsCurrent > maxIndex) newsCurrent = maxIndex;

      var wrapWidth = newsWrap.getBoundingClientRect().width;
      var cardWidth = (wrapWidth - gap * (visible - 1)) / visible;
      newsCards.forEach(function (card) {
        card.style.width = cardWidth + 'px';
      });
      newsTrack.style.transform = 'translateX(-' + (newsCurrent * (cardWidth + gap)) + 'px)';

      if (newsPrev) newsPrev.disabled = newsCurrent <= 0;
      if (newsNext) newsNext.disabled = newsCurrent >= maxIndex;
    }

    if (newsPrev) {
      newsPrev.addEventListener('click', function () {
        if (newsCurrent > 0) {
          newsCurrent--;
          updateNewsSlider();
        }
      });
    }

    if (newsNext) {
      newsNext.addEventListener('click', function () {
        var maxIndex = Math.max(0, newsCards.length - getNewsVisible());
        if (newsCurrent < maxIndex) {
          newsCurrent++;
          updateNewsSlider();
        }
      });
    }

    window.addEventListener('resize', updateNewsSlider);
    updateNewsSlider();
  }

  var communityTrack = document.getElementById('community-track');
  var communityWrap = document.getElementById('community-track-wrap');
  var updateCommunitySlider = null;

  if (communityTrack && communityWrap) {
    var communityCards = communityTrack.querySelectorAll('.news-card');
    var communityCurrent = 0;
    var communityPrev = document.querySelector('.community-nav--prev');
    var communityNext = document.querySelector('.community-nav--next');

    function getCommunityVisible() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function getCommunityGap() {
      return window.innerWidth <= 768 ? 16 : 24;
    }

    updateCommunitySlider = function () {
      var visible = getCommunityVisible();
      var gap = getCommunityGap();
      var maxIndex = Math.max(0, communityCards.length - visible);
      if (communityCurrent > maxIndex) communityCurrent = maxIndex;

      var wrapWidth = communityWrap.getBoundingClientRect().width;
      if (wrapWidth <= 0) return;

      var cardWidth = (wrapWidth - gap * (visible - 1)) / visible;
      communityCards.forEach(function (card) {
        card.style.width = cardWidth + 'px';
      });
      communityTrack.style.transform = 'translateX(-' + (communityCurrent * (cardWidth + gap)) + 'px)';

      if (communityPrev) communityPrev.disabled = communityCurrent <= 0;
      if (communityNext) communityNext.disabled = communityCurrent >= maxIndex;
      scheduleScrollRadiusEffects();
    };

    if (communityPrev) {
      communityPrev.addEventListener('click', function () {
        if (communityCurrent > 0) {
          communityCurrent--;
          updateCommunitySlider();
        }
      });
    }

    if (communityNext) {
      communityNext.addEventListener('click', function () {
        var maxIndex = Math.max(0, communityCards.length - getCommunityVisible());
        if (communityCurrent < maxIndex) {
          communityCurrent++;
          updateCommunitySlider();
        }
      });
    }

    window.addEventListener('resize', updateCommunitySlider);
    updateCommunitySlider();
  }

  var communityToggle = document.getElementById('community-toggle');
  var communityPanel = document.getElementById('community-panel');
  if (communityToggle && communityPanel) {
    communityToggle.addEventListener('click', function () {
      var isOpen = communityPanel.classList.toggle('is-open');
      communityToggle.classList.toggle('is-open', isOpen);
      communityToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      communityPanel.hidden = !isOpen;
      communityToggle.textContent = isOpen ? 'Hide Community' : 'View Community';
      if (isOpen) {
        scheduleScrollRadiusEffects();
        refreshCommunityLazyImages();
      }
      if (isOpen && updateCommunitySlider) {
        requestAnimationFrame(updateCommunitySlider);
      }
    });
  }

  function initPathwayAccordion(listEl) {
    if (!listEl) return;

    var pathways = listEl.querySelectorAll('.pathway');
    var hoverCapable = window.matchMedia('(hover: hover)').matches;

    function setPathwayOpen(pathway, open) {
      var header = pathway.querySelector('.pathway__header');
      var panel = pathway.querySelector('.pathway__panel');
      if (!header || !panel) return;

      pathway.classList.toggle('is-open', open);
      header.setAttribute('aria-expanded', open ? 'true' : 'false');
      panel.hidden = !open;
    }

    function closeAllExcept(except) {
      pathways.forEach(function (pathway) {
        if (pathway !== except) setPathwayOpen(pathway, false);
      });
    }

    pathways.forEach(function (pathway) {
      var header = pathway.querySelector('.pathway__header');
      if (!header) return;

      header.addEventListener('click', function () {
        var willOpen = !pathway.classList.contains('is-open');
        closeAllExcept(willOpen ? pathway : null);
        setPathwayOpen(pathway, willOpen);
      });

      if (hoverCapable) {
        pathway.addEventListener('mouseenter', function () {
          closeAllExcept(pathway);
          setPathwayOpen(pathway, true);
        });

        pathway.addEventListener('mouseleave', function () {
          setPathwayOpen(pathway, false);
        });
      }
    });

    if (window.location.hash) {
      var target = document.getElementById(window.location.hash.slice(1));
      if (target && target.classList.contains('pathway') && listEl.contains(target)) {
        closeAllExcept(target);
        setPathwayOpen(target, true);
        window.setTimeout(function () {
          target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
      }
    }
  }

  initPathwayAccordion(document.getElementById('pathways-list'));
  initPathwayAccordion(document.getElementById('pricing-list'));

  var weekRange = document.getElementById('week-range');
  if (weekRange && !weekRange.textContent.trim()) {
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var now = new Date();
    var day = now.getDay();
    var monday = new Date(now);
    monday.setDate(now.getDate() - ((day + 6) % 7));
    var sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);

    function formatWeekDate(d) {
      return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    weekRange.textContent = 'From ' + formatWeekDate(monday) + ' to ' + formatWeekDate(sunday);
  }

  function compactArtCalendar(table) {
    if (!table || table.dataset.compacted === 'true') {
      return;
    }

    table.querySelectorAll('tbody tr').forEach(function (row) {
      var dataCells = row.querySelectorAll('td:not(.time-col)');
      var hasContent = false;
      dataCells.forEach(function (cell) {
        if (cell.textContent.trim()) {
          hasContent = true;
        }
      });
      if (!hasContent) {
        row.remove();
      }
    });

    var headerRow = table.querySelector('thead tr');
    if (headerRow) {
      for (var col = headerRow.cells.length - 1; col >= 1; col--) {
        var columnEmpty = true;
        table.querySelectorAll('tbody tr').forEach(function (row) {
          var cell = row.cells[col];
          if (cell && cell.textContent.trim()) {
            columnEmpty = false;
          }
        });
        if (columnEmpty) {
          table.querySelectorAll('tr').forEach(function (row) {
            if (row.cells[col]) {
              row.deleteCell(col);
            }
          });
        }
      }
    }

    table.querySelectorAll('tbody td:not(.time-col)').forEach(function (cell) {
      var html = cell.innerHTML.trim();
      if (!html || html.indexOf('<br') === -1) {
        return;
      }
      var names = html.split(/<br\s*\/?>/i).map(function (part) {
        return part.replace(/<[^>]+>/g, '').trim();
      }).filter(Boolean);
      cell.innerHTML = names.map(function (name) {
        return '<span class="art-student">' + name + '</span>';
      }).join('');
    });

    table.dataset.compacted = 'true';
  }

  var artCalendarRoot = document.getElementById('art-calendar');
  if (artCalendarRoot) {
    var watchArtCalendar = function () {
      var table = artCalendarRoot.querySelector('.art-timetable');
      if (table) {
        compactArtCalendar(table);
      }
    };

    watchArtCalendar();

    if ('MutationObserver' in window) {
      new MutationObserver(watchArtCalendar).observe(artCalendarRoot, {
        childList: true,
        subtree: true
      });
    }
  }
})();

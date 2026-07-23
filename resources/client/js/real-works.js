function matchesWorkFilter(tags, filter) {
  return filter === 'all' || tags.indexOf(filter) !== -1;
}

function nextWorkImageIndex(current, total, direction) {
  return (current + direction + total) % total;
}

function initRealWorks() {
  var root = document.querySelector('[data-real-works-root]');

  if (!root) {
    return;
  }

  var filterButtons = Array.prototype.slice.call(root.querySelectorAll('[data-work-filter]'));
  var cases = Array.prototype.slice.call(root.querySelectorAll('[data-work-case]'));

  filterButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var filter = button.getAttribute('data-work-filter');

      filterButtons.forEach(function (candidate) {
        candidate.setAttribute('aria-pressed', candidate === button ? 'true' : 'false');
      });

      cases.forEach(function (item) {
        var tags = (item.getAttribute('data-work-tags') || '').split(/\s+/).filter(Boolean);
        item.hidden = !matchesWorkFilter(tags, filter);
      });
    });
  });

  var lightbox = document.querySelector('[data-work-lightbox]');
  var imageButtons = Array.prototype.slice.call(root.querySelectorAll('[data-work-image]'));
  var activeIndex = 0;
  var returnFocus = null;

  function renderLightbox() {
    var source = imageButtons[activeIndex];
    var image = lightbox.querySelector('[data-work-lightbox-image]');

    image.src = source.currentSrc || source.src;
    image.alt = source.alt;
    lightbox.querySelector('[data-work-lightbox-caption]').textContent = source.alt;
  }

  function openLightbox(index) {
    activeIndex = index;
    returnFocus = imageButtons[index].closest('button');
    renderLightbox();
    lightbox.hidden = false;
    document.body.classList.add('has-open-lightbox');
    lightbox.querySelector('[data-work-lightbox-close]').focus();
  }

  function closeLightbox() {
    lightbox.hidden = true;
    document.body.classList.remove('has-open-lightbox');

    if (returnFocus) {
      returnFocus.focus();
    }
  }

  imageButtons.forEach(function (image, index) {
    image.closest('button').addEventListener('click', function () {
      openLightbox(index);
    });
  });

  lightbox.querySelector('[data-work-lightbox-close]').addEventListener('click', closeLightbox);
  lightbox.querySelector('[data-work-lightbox-prev]').addEventListener('click', function () {
    activeIndex = nextWorkImageIndex(activeIndex, imageButtons.length, -1);
    renderLightbox();
  });
  lightbox.querySelector('[data-work-lightbox-next]').addEventListener('click', function () {
    activeIndex = nextWorkImageIndex(activeIndex, imageButtons.length, 1);
    renderLightbox();
  });
  lightbox.addEventListener('click', function (event) {
    if (event.target === lightbox) {
      closeLightbox();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (lightbox.hidden) {
      return;
    }

    if (event.key === 'Escape') {
      closeLightbox();
    }
    if (event.key === 'ArrowLeft') {
      lightbox.querySelector('[data-work-lightbox-prev]').click();
    }
    if (event.key === 'ArrowRight') {
      lightbox.querySelector('[data-work-lightbox-next]').click();
    }
    if (event.key === 'Tab') {
      var focusable = Array.prototype.slice.call(lightbox.querySelectorAll('button:not([disabled])'));
      var first = focusable[0];
      var last = focusable[focusable.length - 1];

      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  Array.prototype.slice.call(root.querySelectorAll('[data-work-video]')).forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var poster = trigger.querySelector('img');
      var video = document.createElement('video');

      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.preload = 'metadata';
      video.poster = poster.currentSrc || poster.src;
      video.src = trigger.getAttribute('data-video-src');
      video.setAttribute('aria-label', trigger.getAttribute('aria-label'));
      trigger.replaceWith(video);
    }, { once: true });
  });
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRealWorks);
  } else {
    initRealWorks();
  }
}

module.exports = {
  matchesWorkFilter: matchesWorkFilter,
  nextWorkImageIndex: nextWorkImageIndex,
  initRealWorks: initRealWorks,
};

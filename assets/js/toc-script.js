function fehrestinoToggle(el) {
    var wrapper = el.closest('.custom-toc-wrapper');
    if (!wrapper) return;
    wrapper.classList.toggle('is-closed');
    var isClosed = wrapper.classList.contains('is-closed');
    el.setAttribute('aria-expanded', isClosed ? 'false' : 'true');
}

document.addEventListener('DOMContentLoaded', function () {
    var settings = (typeof fehrestinoSettings !== 'undefined') ? fehrestinoSettings : { smoothScroll: true, scrollspy: true };
    var wrapper = document.getElementById('customToc');
    if (!wrapper) return;

    var header = wrapper.querySelector('.custom-toc-header');
    if (header) {
        header.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fehrestinoToggle(header);
            }
        });
    }

    var links = wrapper.querySelectorAll('.custom-toc-links a');

    if (settings.smoothScroll) {
        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var targetId = link.getAttribute('href');
                if (!targetId || targetId.charAt(0) !== '#') return;
                var target = document.querySelector(targetId);
                if (!target) return;
                e.preventDefault();
                var offset = 90;
                var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: top, behavior: 'smooth' });
                history.pushState(null, '', targetId);
            });
        });
    }

    if (settings.scrollspy && 'IntersectionObserver' in window) {
        var headingIds = [];
        links.forEach(function (link) {
            var id = link.getAttribute('href');
            if (id && id.charAt(0) === '#') headingIds.push(id.substring(1));
        });

        var headingEls = headingIds
            .map(function (id) { return document.getElementById(id); })
            .filter(Boolean);

        if (headingEls.length) {
            var activeId = null;

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        activeId = entry.target.id;
                    }
                });

                links.forEach(function (link) {
                    var isActive = link.getAttribute('href') === '#' + activeId;
                    link.classList.toggle('is-active', isActive);
                });
            }, {
                rootMargin: '-90px 0px -70% 0px',
                threshold: 0
            });

            headingEls.forEach(function (el) { observer.observe(el); });
        }
    }
});

// Keep the storefront header lightweight on mobile by toggling a compact menu.
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.querySelector('[data-mobile-nav-toggle]');
  const panel = document.querySelector('[data-mobile-nav-panel]');

  if (!toggle || !panel) {
    return;
  }

  const closeMenu = () => {
    toggle.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
  };

  const openMenu = () => {
    toggle.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
  };

  toggle.addEventListener('click', function () {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';

    if (isOpen) {
      closeMenu();
      return;
    }

    openMenu();
  });

  document.addEventListener('click', function (event) {
    if (panel.hidden) {
      return;
    }

    if (!panel.contains(event.target) && !toggle.contains(event.target)) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeMenu();
    }
  });

  const mediaQuery = window.matchMedia('(min-width: 721px)');
  const handleWidthChange = (event) => {
    if (event.matches) {
      closeMenu();
    }
  };

  if (typeof mediaQuery.addEventListener === 'function') {
    mediaQuery.addEventListener('change', handleWidthChange);
  } else if (typeof mediaQuery.addListener === 'function') {
    mediaQuery.addListener(handleWidthChange);
  }
});

// Mobile filter drawers for admin list pages.
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-filter-panel]').forEach((panel) => {
    const toggle = panel.querySelector('[data-filter-toggle]');
    const body = panel.querySelector('[data-filter-body]');

    if (!toggle || !body) {
      return;
    }

    const updateToggle = () => {
      const isOpen = body.classList.contains('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggle.classList.toggle('is-active', isOpen);
    };

    toggle.addEventListener('click', function () {
      body.classList.toggle('is-open');
      updateToggle();
    });

    updateToggle();
  });
});

// Catalogue note filters live in a dedicated asset so the PHP template stays
// focused on markup while the picker remains progressively enhanced.
document.addEventListener('DOMContentLoaded', function () {
  const filtersToggle = document.getElementById('toggle-catalog-filters');
  const filtersBody = document.getElementById('catalog-filters-body');
  const toggleButton = document.getElementById('toggle-note-filters');
  const noteFilters = document.getElementById('catalog-note-filters');

  if (filtersToggle && filtersBody) {
    const updateFiltersToggle = () => {
      const isOpen = filtersBody.classList.contains('is-open');
      filtersToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      filtersToggle.classList.toggle('is-active', isOpen);
    };

    filtersToggle.addEventListener('click', function () {
      filtersBody.classList.toggle('is-open');
      updateFiltersToggle();
    });

    updateFiltersToggle();
  }

  if (toggleButton && noteFilters) {
    toggleButton.classList.toggle('is-active', noteFilters.classList.contains('is-open'));

    toggleButton.addEventListener('click', function () {
      noteFilters.classList.toggle('is-open');
      toggleButton.classList.toggle('is-active', noteFilters.classList.contains('is-open'));
      toggleButton.setAttribute(
        'aria-expanded',
        noteFilters.classList.contains('is-open') ? 'true' : 'false'
      );
    });
  }

  document.querySelectorAll('[data-note-picker]').forEach((picker) => {
    const inputName = picker.dataset.inputName;
    const notes = JSON.parse(picker.dataset.notes || '[]');
    const searchInput = picker.querySelector('[data-note-search]');
    const dropdown = picker.querySelector('[data-note-dropdown]');
    const selectedWrap = picker.querySelector('[data-note-selected]');
    const hiddenInputsWrap = picker.querySelector('[data-note-inputs]');

    if (!searchInput || !dropdown || !selectedWrap || !hiddenInputsWrap) {
      return;
    }

    const getSelectedIds = () => {
      return Array.from(hiddenInputsWrap.querySelectorAll('input')).map((input) =>
        Number(input.value)
      );
    };

    const addHiddenInput = (id) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = inputName;
      input.value = String(id);
      hiddenInputsWrap.appendChild(input);
    };

    const removeHiddenInput = (id) => {
      const input = hiddenInputsWrap.querySelector(`input[value="${id}"]`);
      if (input) {
        input.remove();
      }
    };

    const renderSelected = () => {
      const selectedIds = getSelectedIds();

      selectedWrap.innerHTML = '';

      selectedIds.forEach((id) => {
        const note = notes.find((item) => Number(item.id) === Number(id));
        if (!note) {
          return;
        }

        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'catalog-note-selected-chip';
        chip.dataset.removeNote = String(note.id);
        chip.innerHTML = `<span>${note.name}</span><span aria-hidden="true">x</span>`;

        chip.addEventListener('click', function () {
          removeHiddenInput(note.id);
          renderSelected();
          renderDropdown(searchInput.value.trim());
        });

        selectedWrap.appendChild(chip);
      });

      if (selectedIds.length === 0) {
        const empty = document.createElement('span');
        empty.className = 'catalog-note-empty';
        empty.textContent = 'No notes selected';
        selectedWrap.appendChild(empty);
      }
    };

    const renderDropdown = (query = '') => {
      const normalizedQuery = query.toLowerCase();
      const selectedIds = getSelectedIds();

      const filtered = notes.filter((note) => {
        const matchesQuery =
          normalizedQuery === '' || note.name.toLowerCase().includes(normalizedQuery);
        const notSelected = !selectedIds.includes(Number(note.id));
        return matchesQuery && notSelected;
      });

      dropdown.innerHTML = '';

      if (filtered.length === 0) {
        dropdown.classList.remove('is-open');

        if (normalizedQuery !== '') {
          const empty = document.createElement('div');
          empty.className = 'catalog-note-dropdown-empty';
          empty.textContent = 'No matching notes';
          dropdown.appendChild(empty);
          dropdown.classList.add('is-open');
        }

        return;
      }

      filtered.slice(0, 8).forEach((note) => {
        const option = document.createElement('button');
        option.type = 'button';
        option.className = 'catalog-note-option';
        option.textContent = note.name;

        option.addEventListener('click', function () {
          addHiddenInput(note.id);
          searchInput.value = '';
          renderSelected();
          renderDropdown('');
          searchInput.focus();
        });

        dropdown.appendChild(option);
      });

      dropdown.classList.add('is-open');
    };

    searchInput.addEventListener('input', function () {
      renderDropdown(searchInput.value.trim());
    });

    searchInput.addEventListener('focus', function () {
      renderDropdown(searchInput.value.trim());
    });

    document.addEventListener('click', function (event) {
      if (!picker.contains(event.target)) {
        dropdown.classList.remove('is-open');
      }
    });

    renderSelected();
  });
});

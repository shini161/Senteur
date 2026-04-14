// Admin product form enhancements keep variant management practical without
// pushing dynamic markup concerns into the PHP templates.
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('[data-admin-product-form]');

  if (!form) {
    return;
  }

  const variantList = form.querySelector('[data-variant-list]');
  const variantTemplate = form.querySelector('[data-variant-template]');
  const addVariantButton = form.querySelector('[data-variant-add]');
  const slugButton = form.querySelector('[data-slug-generate]');
  const slugTarget = form.querySelector('[data-slug-target]');
  const slugName = form.querySelector('[data-slug-name]');
  const slugConcentration = form.querySelector('[data-slug-concentration]');
  const notePickers = Array.from(form.querySelectorAll('[data-note-picker]'));

  const updateVariantCards = () => {
    const cards = Array.from(form.querySelectorAll('[data-variant-card]'));

    cards.forEach((card, index) => {
      const title = card.querySelector('[data-variant-title]');
      const sizeInput = card.querySelector('[data-variant-size]');
      const removeButton = card.querySelector('[data-variant-remove]');
      const sizeValue = sizeInput ? sizeInput.value.trim() : '';

      if (title) {
        title.textContent = sizeValue !== '' ? `${sizeValue} ml variant` : `Variant ${index + 1}`;
      }

      if (removeButton) {
        removeButton.hidden = cards.length === 1;
      }
    });
  };

  if (variantList && variantTemplate && addVariantButton) {
    addVariantButton.addEventListener('click', function () {
      const nextIndex = Number(variantList.dataset.nextIndex || '0');
      const nextMarkup = variantTemplate.innerHTML.replaceAll('__INDEX__', String(nextIndex));

      variantList.insertAdjacentHTML('beforeend', nextMarkup);
      variantList.dataset.nextIndex = String(nextIndex + 1);
      updateVariantCards();
    });

    variantList.addEventListener('click', function (event) {
      const target = event.target;

      if (!(target instanceof HTMLElement)) {
        return;
      }

      const removeButton = target.closest('[data-variant-remove]');

      if (!removeButton) {
        return;
      }

      const card = removeButton.closest('[data-variant-card]');

      if (card) {
        card.remove();
        updateVariantCards();
      }
    });

    variantList.addEventListener('input', function (event) {
      const target = event.target;

      if (target instanceof HTMLElement && target.matches('[data-variant-size]')) {
        updateVariantCards();
      }
    });

    updateVariantCards();
  }

  if (slugButton && slugTarget && slugName) {
    slugButton.addEventListener('click', function () {
      const base = [slugName.value.trim(), slugConcentration ? slugConcentration.value.trim() : '']
        .filter(Boolean)
        .join(' ');

      slugTarget.value = base
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    });
  }

  notePickers.forEach((picker) => {
    const inputName = picker.dataset.inputName || '';
    const notes = JSON.parse(picker.dataset.notes || '[]');
    const searchInput = picker.querySelector('[data-note-search]');
    const dropdown = picker.querySelector('[data-note-dropdown]');
    const selectedWrap = picker.querySelector('[data-note-selected]');
    const hiddenInputsWrap = picker.querySelector('[data-note-inputs]');
    const countBadge = picker.closest('[data-note-stage]')?.querySelector('[data-note-stage-count]');

    if (!searchInput || !dropdown || !selectedWrap || !hiddenInputsWrap || inputName === '') {
      return;
    }

    const getSelectedIds = () => {
      return Array.from(hiddenInputsWrap.querySelectorAll('input')).map((input) => Number(input.value));
    };

    const setCountBadge = () => {
      const selectedCount = getSelectedIds().length;

      if (countBadge) {
        countBadge.textContent = selectedCount === 0 ? 'None selected' : `${selectedCount} selected`;
      }
    };

    const addHiddenInput = (id) => {
      if (hiddenInputsWrap.querySelector(`input[value="${id}"]`)) {
        return;
      }

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

      setCountBadge();
    };

    const renderDropdown = (query = '') => {
      const normalizedQuery = query.toLowerCase();
      const selectedIds = getSelectedIds();

      const filtered = notes.filter((note) => {
        const matchesQuery = normalizedQuery === '' || note.name.toLowerCase().includes(normalizedQuery);
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

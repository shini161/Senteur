<?php
// Public catalogue page with filter controls, advanced note selection, product
// cards, and pagination that preserves the current filter state.
?>
<section class="products-hero">
    <p class="section-kicker">Catalogue</p>
    <h1>Discover perfumes worth wearing</h1>
    <p class="lead">
        Explore curated fragrances with clean variant pricing and availability.
    </p>
</section>

<section class="panel" style="margin-bottom: 1.5rem;">
    <?php // Primary catalogue filters are server-rendered from repository metadata. ?>
    <form method="GET" action="/products" class="auth-form">
        <div class="catalog-filters-grid">
            <div class="form-group catalog-filter-search">
                <label for="search">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Name, brand, concentration, or description"
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id">
                    <option value="0">All brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option
                            value="<?= (int) $brand['id'] ?>"
                            <?= (int) ($filters['brand_id'] ?? 0) === (int) $brand['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fragrance_type_id">Type</label>
                <select id="fragrance_type_id" name="fragrance_type_id">
                    <option value="0">All types</option>
                    <?php foreach ($fragranceTypes as $type): ?>
                        <option
                            value="<?= (int) $type['id'] ?>"
                            <?= (int) ($filters['fragrance_type_id'] ?? 0) === (int) $type['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">All</option>
                    <?php foreach ($genders as $gender): ?>
                        <option
                            value="<?= htmlspecialchars($gender) ?>"
                            <?= ($filters['gender'] ?? '') === $gender ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($gender)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort</label>
                <select id="sort" name="sort">
                    <?php foreach ($sortOptions as $value => $label): ?>
                        <option
                            value="<?= htmlspecialchars($value) ?>"
                            <?= ($filters['sort'] ?? 'newest') === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="catalog-filter-actions">
            <button type="submit" class="auth-button">Apply filters</button>
            <a href="/products" class="button-secondary">Reset</a>
            <button
                type="button"
                class="button-secondary"
                id="toggle-note-filters"
                aria-expanded="<?= !empty($filters['top_note_ids']) || !empty($filters['middle_note_ids']) || !empty($filters['base_note_ids']) ? 'true' : 'false' ?>">
                Advanced note filters
            </button>
            <span class="muted"><?= (int) $totalProducts ?> result(s)</span>
        </div>

        <?php
        // The note picker JavaScript reads a serialized copy of the note list so
        // advanced filters can stay interactive without another API endpoint.
        $showNoteFilters = !empty($filters['top_note_ids']) || !empty($filters['middle_note_ids']) || !empty($filters['base_note_ids']);
        $allNotesJson = htmlspecialchars(json_encode(array_map(
            static fn(array $note): array => [
                'id' => (int) $note['id'],
                'name' => (string) $note['name'],
            ],
            $notes
        )), ENT_QUOTES, 'UTF-8');
        ?>

        <div class="catalog-note-filters <?= $showNoteFilters ? 'is-open' : '' ?>" id="catalog-note-filters">
            <div class="catalog-note-filters-header">
                <h3>Advanced note filters</h3>
                <p class="muted">Add notes by accord stage. Click a selected note again to remove it.</p>
            </div>

            <?php
            // Each note group maps to a distinct query-string field handled by
            // `ProductService::normalizePublicFilters()`.
            $noteGroups = [
                'top' => [
                    'label' => 'Top notes',
                    'input_name' => 'top_note_ids[]',
                    'selected' => $filters['top_note_ids'] ?? [],
                ],
                'middle' => [
                    'label' => 'Middle notes',
                    'input_name' => 'middle_note_ids[]',
                    'selected' => $filters['middle_note_ids'] ?? [],
                ],
                'base' => [
                    'label' => 'Base notes',
                    'input_name' => 'base_note_ids[]',
                    'selected' => $filters['base_note_ids'] ?? [],
                ],
            ];
            ?>

            <div class="catalog-note-groups">
                <?php foreach ($noteGroups as $key => $group): ?>
                    <div
                        class="catalog-note-filter-group"
                        data-note-picker
                        data-input-name="<?= htmlspecialchars($group['input_name']) ?>"
                        data-notes="<?= $allNotesJson ?>">
                        <label><?= htmlspecialchars($group['label']) ?></label>

                        <div class="catalog-note-selected" data-note-selected>
                            <?php foreach ($group['selected'] as $selectedId): ?>
                                <?php
                                $selectedNote = null;
                                foreach ($notes as $note) {
                                    if ((int) $note['id'] === (int) $selectedId) {
                                        $selectedNote = $note;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($selectedNote): ?>
                                    <button
                                        type="button"
                                        class="catalog-note-selected-chip"
                                        data-remove-note="<?= (int) $selectedNote['id'] ?>">
                                        <span><?= htmlspecialchars($selectedNote['name']) ?></span>
                                        <span aria-hidden="true">×</span>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="catalog-note-input-wrap">
                            <input
                                type="text"
                                class="catalog-note-search"
                                data-note-search
                                placeholder="Search notes...">
                            <div class="catalog-note-dropdown" data-note-dropdown></div>
                        </div>

                        <div data-note-inputs>
                            <?php foreach ($group['selected'] as $selectedId): ?>
                                <input type="hidden" name="<?= htmlspecialchars($group['input_name']) ?>" value="<?= (int) $selectedId ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
</section>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <p>No products matched your filters.</p>
    </div>
<?php else: ?>
    <section class="products-grid">
        <?php foreach ($products as $product): ?>
            <?php // Each card is already enriched with brand, price, and sellable state. ?>
            <article class="product-card">
                <a href="/products/<?= htmlspecialchars($product['slug']) ?>" class="product-card-link">
                    <div class="product-card-media">
                        <?php if (!empty($product['image_url'])): ?>
                            <img
                                src="/<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="product-placeholder">SENTEUR</div>
                        <?php endif; ?>
                    </div>

                    <div class="product-card-body">
                        <div class="product-card-brand">
                            <?= htmlspecialchars($product['brand_name']) ?>
                        </div>

                        <h2 class="product-card-title"><?= htmlspecialchars($product['name']) ?></h2>

                        <?php if (!empty($product['concentration_label'])): ?>
                            <p class="product-card-subtitle"><?= htmlspecialchars($product['concentration_label']) ?></p>
                        <?php endif; ?>

                        <div class="product-card-meta">
                            <span class="product-card-price">
                                €<?= number_format((float) $product['price'], 2) ?>
                            </span>

                            <span class="product-card-status">
                                <?= !empty($product['is_sellable']) ? 'In stock' : 'Unavailable' ?>
                            </span>
                        </div>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php if (($totalPages ?? 1) > 1): ?>
    <?php
    $queryParams = $filters;
    unset($queryParams['page']);

    // Rebuild pagination URLs while preserving the currently applied filters.
    $buildPageUrl = static function (int $pageNumber) use ($queryParams): string {
        $params = array_merge($queryParams, ['page' => $pageNumber]);

        return '/products?' . http_build_query(array_filter(
            $params,
            static fn($value) => $value !== '' && $value !== 0 && $value !== '0'
        ));
    };
    ?>

    <nav class="catalog-pagination">
        <?php if (($currentPage ?? 1) > 1): ?>
            <a href="<?= htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" class="button-secondary">Previous</a>
        <?php else: ?>
            <span class="button-secondary pagination-disabled">Previous</span>
        <?php endif; ?>

        <span class="muted">
            Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?>
        </span>

        <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
            <a href="<?= htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" class="button-secondary">Next</a>
        <?php else: ?>
            <span class="button-secondary pagination-disabled">Next</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<script>
    // The advanced note picker stays client-side to keep the PHP route layer
    // simple while still giving the catalogue a richer filtering experience.
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggle-note-filters');
        const noteFilters = document.getElementById('catalog-note-filters');

        if (toggleButton && noteFilters) {
            toggleButton.addEventListener('click', function() {
                noteFilters.classList.toggle('is-open');
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
                return Array.from(hiddenInputsWrap.querySelectorAll('input'))
                    .map((input) => Number(input.value));
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
                    chip.innerHTML = `<span>${note.name}</span><span aria-hidden="true">×</span>`;

                    chip.addEventListener('click', function() {
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

                    option.addEventListener('click', function() {
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

            searchInput.addEventListener('input', function() {
                renderDropdown(searchInput.value.trim());
            });

            searchInput.addEventListener('focus', function() {
                renderDropdown(searchInput.value.trim());
            });

            document.addEventListener('click', function(event) {
                if (!picker.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                }
            });

            renderSelected();
        });
    });
</script>

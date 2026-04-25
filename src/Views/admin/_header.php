<?php
$adminHeaderTitle = (string) ($adminHeaderTitle ?? 'Admin');
$adminHeaderLead = (string) ($adminHeaderLead ?? '');
$adminHeaderKicker = (string) ($adminHeaderKicker ?? 'Admin');
$adminHeaderSection = (string) ($adminHeaderSection ?? '');
$adminHeaderClass = trim('admin-page-header ' . (string) ($adminHeaderClass ?? ''));
$adminHeaderActions = is_array($adminHeaderActions ?? null) ? $adminHeaderActions : [];
$adminHeaderBackLink = is_array($adminHeaderBackLink ?? null) ? $adminHeaderBackLink : null;

$adminNavItems = [
    ['key' => 'orders', 'label' => 'Orders', 'href' => '/admin/orders'],
    ['key' => 'products', 'label' => 'Products', 'href' => '/admin/products'],
    ['key' => 'notes', 'label' => 'Notes', 'href' => '/admin/notes'],
    ['key' => 'catalog', 'label' => 'Catalog Data', 'href' => '/admin/catalog'],
];
?>
<div class="card <?= htmlspecialchars($adminHeaderClass) ?>">
    <div class="admin-page-header-top">
        <div class="admin-page-header-copy">
            <?php if ($adminHeaderBackLink !== null): ?>
                <a
                    href="<?= htmlspecialchars((string) ($adminHeaderBackLink['href'] ?? '#')) ?>"
                    class="admin-page-back-link">
                    <?= htmlspecialchars((string) ($adminHeaderBackLink['label'] ?? 'Back')) ?>
                </a>
            <?php endif; ?>

            <p class="section-kicker"><?= htmlspecialchars($adminHeaderKicker) ?></p>
            <h1><?= htmlspecialchars($adminHeaderTitle) ?></h1>

            <?php if ($adminHeaderLead !== ''): ?>
                <p class="lead admin-page-header-lead">
                    <?= htmlspecialchars($adminHeaderLead) ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ($adminHeaderActions !== []): ?>
            <div class="admin-page-header-actions">
                <?php foreach ($adminHeaderActions as $action): ?>
                    <?php
                    $type = (string) ($action['type'] ?? 'link');
                    $className = trim((string) ($action['class'] ?? 'button-secondary'));
                    $label = (string) ($action['label'] ?? '');
                    ?>
                    <?php if ($type === 'link'): ?>
                        <a
                            href="<?= htmlspecialchars((string) ($action['href'] ?? '#')) ?>"
                            class="<?= htmlspecialchars($className) ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    <?php elseif ($type === 'form'): ?>
                        <form
                            method="POST"
                            action="<?= htmlspecialchars((string) ($action['action'] ?? '#')) ?>"
                            class="<?= htmlspecialchars(trim('admin-page-header-form ' . (string) ($action['form_class'] ?? ''))) ?>">
                            <?= \App\Core\Csrf::input() ?>
                            <button
                                type="submit"
                                class="<?= htmlspecialchars($className) ?>"
                                <?php if (! empty($action['disabled'])): ?>disabled<?php endif; ?>>
                                <?= htmlspecialchars($label) ?>
                            </button>
                        </form>
                    <?php elseif ($type === 'badge'): ?>
                        <span class="<?= htmlspecialchars($className !== '' ? $className : 'badge') ?>">
                            <?= htmlspecialchars($label) ?>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <nav class="admin-page-nav" aria-label="Admin sections">
        <?php foreach ($adminNavItems as $item): ?>
            <a
                href="<?= htmlspecialchars($item['href']) ?>"
                class="admin-page-nav-link <?= $item['key'] === $adminHeaderSection ? 'is-active' : '' ?>">
                <?= htmlspecialchars($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</div>

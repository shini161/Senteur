<section class="admin-product-page">
    <div class="admin-product-shell">
        <?php
        $adminHeaderTitle = 'Create Product';
        $adminHeaderLead = 'Define the product identity, note profile, and size variants first. Media uploads come right after the initial save.';
        $adminHeaderKicker = 'Admin / Products';
        $adminHeaderSection = 'products';
        $adminHeaderClass = 'admin-product-header';
        $adminHeaderBackLink = [
            'href' => '/admin/products',
            'label' => 'Back to products',
        ];
        $adminHeaderActions = [];

        require __DIR__ . '/../_header.php';
        ?>

        <div class="admin-product-workspace">
            <div class="admin-product-main">
                <form method="POST" action="/admin/products" class="admin-product-form" data-admin-product-form>
                    <?= \App\Core\Csrf::input() ?>
                    <?php require __DIR__ . '/_form.php'; ?>

                    <div class="panel admin-product-submit-panel">
                        <div>
                            <h2>Ready to create</h2>
                            <p class="muted">Save the catalog record, then continue with primary and variant imagery on the edit screen.</p>
                        </div>

                        <button type="submit" class="auth-button">Create product</button>
                    </div>
                </form>
            </div>

            <aside class="admin-product-sidebar">
                <section class="panel admin-product-sidebar-panel">
                    <h2>Before you save</h2>
                    <p class="muted">
                        Keep the base name clean, use a URL-safe slug, choose either Fragrance Notes or a full top/heart/base pyramid when possible, and make variant sizes unique so the storefront can render a tidy product page.
                    </p>
                </section>

                <section class="panel admin-product-sidebar-panel">
                    <h2>After creation</h2>
                    <p class="muted">
                        The edit view lets you upload the primary product shot and dedicated variant images once the first save creates stable IDs.
                    </p>
                </section>
            </aside>
        </div>
    </div>
</section>

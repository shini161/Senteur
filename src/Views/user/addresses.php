<section class="addresses-page">
    <div class="addresses-grid">

        <div class="addresses-card">
            <h1>Addresses</h1>
            <p class="addresses-subtitle">Manage your shipping addresses.</p>

            <?php if (! empty($error)): ?>
                <p class="auth-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (empty($addresses)): ?>
                <p>You have no saved addresses yet.</p>
            <?php else: ?>
                <div class="address-list">
                    <?php foreach ($addresses as $address): ?>
                        <div class="address-item">
                            <div class="address-item-header">
                                <strong><?= htmlspecialchars($address['full_name']) ?></strong>

                                <?php if ((int) $address['is_default'] === 1): ?>
                                    <span class="address-badge">Default</span>
                                <?php endif; ?>
                            </div>

                            <p>
                                <?= htmlspecialchars($address['address_line']) ?><br>
                                <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['postal_code']) ?><br>
                                <?= htmlspecialchars($address['country']) ?>
                            </p>

                            <div class="address-actions">
                                <form method="POST" action="/addresses/default">
                                    <?= \App\Core\Csrf::input() ?>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $address['id']) ?>">
                                    <button
                                        type="submit"
                                        class="button-secondary"
                                        <?= (int) $address['is_default'] === 1 ? 'disabled' : '' ?>>
                                        Set default
                                    </button>
                                </form>

                                <form method="POST" action="/addresses/delete">
                                    <?= \App\Core\Csrf::input() ?>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $address['id']) ?>">
                                    <button type="submit" class="button-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="addresses-card">
            <h2>Add address</h2>
            <p class="addresses-subtitle">Save a delivery address for future orders.</p>

            <?php if ($can_add_address): ?>
                <form method="POST" action="/addresses" class="auth-form">
                    <?= \App\Core\Csrf::input() ?>

                    <div class="form-group">
                        <label for="full_name">Full name</label>
                        <input
                            id="full_name"
                            name="full_name"
                            value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="address_line">Address line</label>
                        <input
                            id="address_line"
                            name="address_line"
                            value="<?= htmlspecialchars($old['address_line'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input
                            id="city"
                            name="city"
                            value="<?= htmlspecialchars($old['city'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal code</label>
                        <input
                            id="postal_code"
                            name="postal_code"
                            value="<?= htmlspecialchars($old['postal_code'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <input
                            id="country"
                            name="country"
                            value="<?= htmlspecialchars($old['country'] ?? '') ?>"
                            required>
                    </div>

                    <label class="checkbox-row">
                        <input
                            type="checkbox"
                            name="is_default"
                            <?= ! empty($old['is_default']) ? 'checked' : '' ?>>
                        Set as default address
                    </label>

                    <button type="submit" class="auth-button">Save address</button>
                </form>
            <?php else: ?>
                <div class="empty-state">
                    <p>You reached the maximum of 10 addresses.</p>
                    <p>Delete one to add a new address.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

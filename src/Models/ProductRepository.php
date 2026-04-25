<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Models\Concerns\ProductRepositoryAdminQueries;
use App\Models\Concerns\ProductRepositoryCatalogueQueries;
use App\Models\Concerns\ProductRepositoryLookupQueries;
use App\Models\Concerns\ProductRepositoryTaxonomyQueries;
use PDO;

/**
 * Repository for storefront catalogue queries, admin product management, and
 * shared product metadata lookups.
 */
class ProductRepository
{
    use ProductRepositoryCatalogueQueries;
    use ProductRepositoryAdminQueries;
    use ProductRepositoryLookupQueries;
    use ProductRepositoryTaxonomyQueries;

    public function __construct(
        private ?PDO $pdo = null
    ) {
        $this->pdo ??= Database::getConnection();
    }
}

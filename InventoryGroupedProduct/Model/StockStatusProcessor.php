<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryGroupedProduct\Model;

use Magento\GroupedProduct\Model\Inventory\ChangeParentStockStatus;
use Magento\InventoryCatalogApi\Model\CompositeProductStockStatusProcessorInterface;

/**
 * @inheritDoc
 */
class StockStatusProcessor implements CompositeProductStockStatusProcessorInterface
{
    /**
     * @var ChangeParentStockStatus
     */
    private ChangeParentStockStatus $changeParentStockStatus;

    /**
     * @param ChangeParentStockStatus $changeParentStockStatus
     */
    public function __construct(
        ChangeParentStockStatus $changeParentStockStatus
    ) {
        $this->changeParentStockStatus = $changeParentStockStatus;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $productIds): void
    {
        foreach ($productIds as $productId) {
            $this->changeParentStockStatus->execute($productId);
        }
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryInStorePickupShipping\Test\Integration\Carrier;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class InStorePickupTest extends TestCase
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        $this->cartRepository = Bootstrap::getObjectManager()->get(CartRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/source_addresses.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/source_pickup_location_attributes.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stocks.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stock_source_links.php
     * @magentoDataFixture ../../../../app/code/Magento/InventorySalesApi/Test/_files/websites_with_stores.php
     * @magentoDataFixture ../../../../app/code/Magento/InventorySalesApi/Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items_eu_stock_only.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryIndexer/Test/_files/reindex_inventory.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/create_in_store_pickup_quote_on_eu_website.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/add_products_from_eu_stock_to_cart.php
     *
     * @magentoConfigFixture store_for_eu_website_store carriers/in_store/active 1
     * @magentoConfigFixture store_for_eu_website_store carriers/in_store/price 5.95
     *
     * @magentoDbIsolation disabled
     */
    public function testShippingCost()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('reserved_order_id', 'in_store_pickup_test_order')
            ->create();
        /** @var \Magento\Quote\Api\Data\CartInterface $cart */
        $cart = current($this->cartRepository->getList($searchCriteria)->getItems());

        $this->assertEquals("in_store_pickup", $cart->getShippingAddress()->getShippingMethod());
        $this->assertEquals(5.95, $cart->getShippingAddress()->getShippingAmount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/sources.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/source_addresses.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stocks.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/stock_source_links.php
     * @magentoDataFixture ../../../../app/code/Magento/InventorySalesApi/Test/_files/websites_with_stores.php
     * @magentoDataFixture ../../../../app/code/Magento/InventorySalesApi/Test/_files/stock_website_sales_channels.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/source_items_eu_stock_only.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryIndexer/Test/_files/reindex_inventory.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/create_in_store_pickup_quote_on_eu_website.php
     * @magentoDataFixture ../../../../app/code/Magento/InventoryInStorePickup/Test/_files/add_products_from_eu_stock_to_cart.php
     *
     * @magentoConfigFixture store_for_eu_website_store carriers/in_store/active 1
     * @magentoConfigFixture store_for_eu_website_store carriers/in_store/price 5.95
     *
     * @magentoAppArea frontend
     *
     * @magentoDbIsolation disabled
     */
    public function testShippingMethodWithoutPickupLocations()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('reserved_order_id', 'in_store_pickup_test_order')
            ->create();
        /** @var \Magento\Quote\Api\Data\CartInterface $cart */
        $cart = current($this->cartRepository->getList($searchCriteria)->getItems());
        $this->assertEmpty($cart->getShippingAddress()->getShippingMethod());
        $this->assertEquals(0, $cart->getShippingAddress()->getShippingAmount());
    }
}
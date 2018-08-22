<?php

namespace B2bapp\UpdateHandler\Observer;

class ProductChangeObserver implements \Magento\Framework\Event\ObserverInterface
{
    public function detectProductChanges(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $product = $observer->getEvent()->getProduct();
        $logger = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Psr\Log\LoggerInterface');
        try {
            $productId = 0;
            if (!$product->getId()) {
                //New product
                $collectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
                $collection = $collectionFactory
                    ->create()
                    ->addAttributeToFilter('name',$product->getName())
                    ->setPageSize(1);

                if ($collection->getSize()) {
                    $productId = $collection->getFirstItem()->getId();
                } else {
                    $logger->info('B2bapp_UpdateHandler - Detect Product changes: Could not get ID for '. $product->getName());
                    return;
                }
            } else {
                $productId = $product->getId();
            }
            $action = '';
            if($observer->getEvent()->getName() == 'catalog_product_save_after') {
                $action = 'change';
            } else if($observer->getEvent()->getName() == 'catalog_product_delete_after') {
                $action = 'delete';
            }

            $this->saveUpdatedProductDetails($product->getSku(), $productId, $action);
            $this->checkIfChildOfAConfigurableProduct($productId);
        } catch (\Exception $e) {
            $logger->info("B2bapp_UpdateHandler - detectProductChanges: Error saving updated entities");
        }
        return $this;
    }

    public function detectBulkProductChanges(\Magento\Framework\Event\Observer $observer)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Psr\Log\LoggerInterface');

        $products = $observer->getEvent()->getProductIds();
        try {
            foreach ($products as $productId) {
                $product = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magento\Catalog\Model\Product')
                    ->load((int)$productId);

                if ($product) {
                    $this->saveUpdatedProductDetails($product->getSku(), $productId, 'change');
                    $this->checkIfChildOfAConfigurableProduct($productId);
                }
            }
        } catch (\Exception $e) {
            $logger->info("B2bapp_UpdateHandler - detectBulkProductChanges: Error saving updated entities");
        }
        return $this;
    }

    public function detectImportProductChanges(\Magento\Framework\Event\Observer $observer)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Psr\Log\LoggerInterface');

        $products = $observer->getEvent()->getBunch();

        try {
            foreach ($products as $product) {
                $productObj = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magento\Catalog\Model\Product')
                    ->loadByAttribute('sku', $product['sku']);

                if ($productObj) {
                    $action = 'change';
                    if($observer->getEvent()->getName() == 'catalog_product_import_bunch_save_after') {
                        $action = 'change';
                    } else if($observer->getEvent()->getName() == 'catalog_product_import_bunch_delete_commit_before') {
                        $action = 'delete';
                    }

                    $this->saveUpdatedProductDetails($productObj->getSku(), $productObj->getId(), $action);
                    $this->checkIfChildOfAConfigurableProduct($productObj->getId());
                }
            }
        } catch (\Exception $e) {
            $logger->info("B2bapp_UpdateHandler - detectImportProductChanges: Error saving updated entities");
        }
        return $this;
    }

    public function detectImportProductDeletes(\Magento\Framework\Event\Observer $observer)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Psr\Log\LoggerInterface');

        $productIds = $observer->getEvent()->getIdsToDelete();
        $productArray = $observer->getEvent()->getBunch();

        try {
            $idx = 0;
            foreach ($productIds as $productId) {
                $sku = $productArray[$idx++]['sku'];
                if ($sku) {

                    $this->saveUpdatedProductDetails($sku, $productId, 'delete');
                    $this->checkIfChildOfAConfigurableProduct($productId);
                }
            }
        } catch (\Exception $e) {
            $logger->info("B2bapp_UpdateHandler - detectImportProductDeletes: Error saving updated entities");
        }
        return $this;
    }

    private function checkIfChildOfAConfigurableProduct($productId)
    {
        $configurableProductType = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable');

        $parentIds = $configurableProductType->getParentIdsByChild($productId);

        if ($parentIds && is_array($parentIds) && count($parentIds)) {
           foreach($parentIds as $parentId) {
               $parent = \Magento\Framework\App\ObjectManager::getInstance()
                   ->create('Magento\Catalog\Model\Product')
                   ->load((int)$parentId);

               if ($parent) {
                   $this->saveUpdatedProductDetails($parent->getSku(), $parentId, 'change');
               }
           }
        }
    }

    private function saveUpdatedProductDetails($sku, $productId, $action)
    {
        $model = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('B2bapp\UpdateHandler\Model\UpdatedEntities');

        $model->load($sku, 'sku');

        $model->addData([
            "entity" => 'product',
            "entity_id" => (int)$productId,
            "action" => $action,
            "sku" => $sku,
            "updated_at" => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            "status" => true
        ]);
        $model->save();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();

        if ($eventName == 'catalog_product_save_after' || $eventName == 'catalog_product_delete_after') {
            $this->detectProductChanges($observer);
        } else if ($eventName == 'catalog_product_attribute_update_before') {
            $this->detectBulkProductChanges($observer);
        } else if ($eventName == 'catalog_product_import_bunch_save_after') {
            $this->detectImportProductChanges($observer);
        } else if ($eventName == 'catalog_product_import_bunch_delete_commit_before') {
            $this->detectImportProductDeletes($observer);
        }

        return $this;
    }
}
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
        try {
            $model = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('B2bapp\UpdateHandler\Model\UpdatedEntities');

            $model->load($product->getSku(), 'sku');

            $action = '';
            if($observer->getEvent()->getName() == 'catalog_product_save_before') {
                $action = 'change';
            } else if($observer->getEvent()->getName() == 'catalog_product_delete_after') {
                $action = 'delete';
            }

            $model->addData([
                "id" => $product->getId(),
                "entity" => 'product',
                "action" => $action,
                "sku" => $product->getSku(),
                "updated_at" => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                "status" => true
            ]);
            $model->save();
        } catch (Exception $e) {
            echo "error saving updated entities";
        }
        return $this;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->detectProductChanges($observer);

        return $this;
    }
}
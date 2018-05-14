<?php

namespace B2bapp\UpdateHandler\Observer;

class ProductChangeObserver implements \Magento\Framework\Event\ObserverInterface
{
    public function detectProductChanges($observer)
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $product = $observer->getEvent()->getProduct();
        if ($product->hasDataChanges()) {
            try {
                $model = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('B2bapp\UpdateHandler\Model\UpdatedEntities');

                $model->load($product->getSku(), 'sku');

                $model->addData([
                    "entity" => 'product',
                    "sku" => $product->getSku(),
                    "updated_at" => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                    "status" => true
                ]);
                $model->save();
            } catch (Exception $e) {
                echo "error saving updated entties";
            }
        }
        return $this;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->detectProductChanges($observer);

        return $this;
    }
}
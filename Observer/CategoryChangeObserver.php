<?php

namespace B2bapp\UpdateHandler\Observer;

class CategoryChangeObserver implements \Magento\Framework\Event\ObserverInterface
{
    public function detectCategoryChanges(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var $category Mage_Catalog_Model_Category
         */
        $category = $observer->getEvent()->getCategory();
        try {
            $logger = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('\Psr\Log\LoggerInterface');

            $categoryId = 0;
            if (!$category->getId()) {
                //New category
                $collectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
                $collection = $collectionFactory
                    ->create()
                    ->addAttributeToFilter('name',$category->getName())
                    ->setPageSize(1);

                if ($collection->getSize()) {
                    $categoryId = $collection->getFirstItem()->getId();
                } else {
                    $logger->log(\Psr\Log\LogLevel::DEBUG, 'B2bapp_UpdateHandler - Detect Category changes: Could not get ID for '. $category->getName());
                    return;
                }
            } else {
                $categoryId = $category->getId();
            }
            $model = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('B2bapp\UpdateHandler\Model\UpdatedEntities');

            $model->load($category->getName(), 'sku');

            $action = '';
            if($observer->getEvent()->getName() == 'catalog_category_save_after') {
                $action = 'change';
            } else if($observer->getEvent()->getName() == 'catalog_category_delete_after') {
                $action = 'delete';
            }

            $model->addData([
                "entity" => 'category',
                "entity_id" => $categoryId,
                "action" => $action,
                "sku" => $category->getName(),
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
        $this->detectCategoryChanges($observer);

        return $this;
    }
}
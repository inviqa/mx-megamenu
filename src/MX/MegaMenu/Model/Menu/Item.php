<?php

namespace MX\MegaMenu\Model\Menu;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Cms\Model\Template\FilterProvider;

class Item extends AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CONTENT_TYPE_CATEGORY = 'category';
    const CONTENT_TYPE_CONTENT = 'wysiwyg';

    /**
     * Get Item Data
     *
     * @param array $item
     * @return array
     */
    public function getItemData($item)
    {
        return [
            'status' => $this->isEnabled($item, 'status'),
            'name' => $item['name'],
            'link' => $this->getItemLink($item),
            'header_status' => $this->isEnabled($item, 'header_status'),
            'header_content' => $this->getDecodedContent($item['header_content']),
            'content_status' => $this->isEnabled($item, 'content_status'),
            'content_content' => $this->getDecodedContent($item['content_content']),
            'content_categories' => $this->getCategories($item),
            'footer_status' => $this->isEnabled($item, 'footer_status'),
            'footer_content' => $this->getDecodedContent($item['footer_content']),
            'leftside_status' => $this->isEnabled($item, 'leftside_status'),
            'leftside_content' => $this->getDecodedContent($item['leftside_content']),
            'rightside_status' => $this->isEnabled($item, 'rightside_status'),
            'rightside_content' => $this->getDecodedContent($item['rightside_content'])
        ];
    }

    /**
     * Get categories
     *
     * @param array $item
     * @return array|string
     */
    protected function getCategories($item)
    {
        if ($item['content_type'] === self::CONTENT_TYPE_CATEGORY) {
            $objectManager = ObjectManager::getInstance();

            $contentCategory = explode('/', $item['content_category']);
            $categoryId = $contentCategory[1];

            /** @var CategoryFactory $categoryFactory */
            $categoryFactory = $objectManager->create(CategoryFactory::class);

            // Parent Category
            /** @var Category $category */
            $category = $categoryFactory->create()->load($categoryId);

            $categories = [
                'category' => $this->getCategoryData($category),
                'children' => []
            ];

            // Subcategories
            $subcategories = $category->getChildrenCategories();
            if ($subcategories) {
                foreach ($subcategories as $subcategory) {
                    $categories['children'][] = $this->getCategoryData($subcategory);
                }
            }

            return $categories;
        }

        return '';
    }

    /**
     * Get Category Data
     *
     * @param Category $category
     * @return array
     */
    protected function getCategoryData($category)
    {
        $result = [];

        if ($category->getId() && $category->getIsActive() == 1) {
            $result = [
                'name' => $category->getName(),
                'link' => $category->getUrl(),
            ];
        }

        return $result;
    }

    /**
     * Is the property enabled
     *
     * @param array $item
     * @param string $property
     * @return boolean
     */
    protected function isEnabled($item, $property)
    {
        return isset($item[$property]) && $item[$property] == self::STATUS_ENABLED;
    }

    /**
     * Get Decoded Content
     *
     * @param string $content
     * @return string
     */
    protected function getDecodedContent($content)
    {
        $objectManager = ObjectManager::getInstance();

        /** @var FilterProvider $filterProvider */
        $filterProvider = $objectManager->create(FilterProvider::class);

        return $filterProvider->getBlockFilter()->filter($content);
    }

    /**
     * Get Item Link
     *
     * @param array $item
     * @return string
     */
    protected function getItemLink($item)
    {
        return !empty($item['link']) ? $item['link'] : 'javascript:;';
    }
}
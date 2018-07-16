<?php

namespace MX\MegaMenu\Model\Menu;

use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Item extends AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const OFFSET_CATEGORY_ID = 1;

    const CONTENT_TYPE_CATEGORY = 'category';
    const CONTENT_TYPE_CONTENT = 'wysiwyg';

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    public function __construct(
        Context $context,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        FilterProvider $filterProvider,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

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
            $contentCategory = explode('/', $item['content_category']);
            if ($contentCategory && isset($contentCategory[self::OFFSET_CATEGORY_ID])) {
                $categoryId = $contentCategory[self::OFFSET_CATEGORY_ID];

                // Parent Category
                /** @var Category $category */
                $category = $this->categoryRepository->get($categoryId);

                $categories = [
                    'category' => $this->getCategoryData($category),
                    'children' => []
                ];

                // Subcategories
                $subcategories = $category->getChildrenCategories();
                foreach ($subcategories as $subcategory) {
                    $categories['children'][] = $this->getCategoryData($subcategory);
                }

                return $categories;
            }
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
                'id' => $category->getId(),
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
        return $this->filterProvider->getBlockFilter()->filter($content);
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
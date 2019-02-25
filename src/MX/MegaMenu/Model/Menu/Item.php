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
use Magento\Framework\Exception\NoSuchEntityException;

class Item extends AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const OFFSET_CATEGORY_ID = 1;

    const LEVEL_DEFAULT = 0;
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;

    const CONTENT_TYPE_CATEGORY = 'category';
    const CONTENT_TYPE_CONTENT = 'wysiwyg';

    const CATEGORY_TYPE_SHOW = 'show';
    const CATEGORY_TYPE_HIDE = 'hide';
    const CATEGORY_TYPE_TOGGLE = 'toggle';

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
            'name' => $this->getDecodedContent($item['name']),
            'link' => $this->getItemLink($item),
            'header_status' => $this->isEnabled($item, 'header_status'),
            'header_content' => $this->getDecodedContent($item['header_content']),
            'content_status' => $this->isEnabled($item, 'content_status'),
            'content_content' => $this->getDecodedContent($item['content_content']),
            'content_categories' => $this->getCategories($item),
            'content_category_type' => $item['content_category_type'],
            'remove_category_anchor' => $item['remove_category_anchor'],
            'custom_class' => $item['custom_class'],
            'footer_status' => $this->isEnabled($item, 'footer_status'),
            'footer_content' => $this->getDecodedContent($item['footer_content']),
            'leftside_status' => $this->isEnabled($item, 'leftside_status'),
            'leftside_content' => $this->getDecodedContent($item['leftside_content']),
            'rightside_status' => $this->isEnabled($item, 'rightside_status'),
            'rightside_content' => $this->getDecodedContent($item['rightside_content']),
            'level' => self::LEVEL_DEFAULT
        ];
    }

    /**
     * Need encode
     *
     * @param string $name
     * @return boolean
     */
    public function needEncode($name)
    {
        return strpos($name, '_content') !== false || strpos($name, 'link') !== false
            || $name === 'name';
    }

    /**
     * Encode entities - do not encode &amp; &quot; &lt; &gt;
     * It's for very special cases: e.g. &reg; &copyright;
     *
     * @param string $content
     * @return string
     */
    public function encodeSpecialCharacters($content)
    {
        return preg_replace('#(&)([^amp|quot|lt|gt].*?)(;)#', '{amp}$2{comma}', $content);
    }

    /**
     * Decode entities - see encode special characters above
     *
     * @param string $content
     * @return string
     */
    public function decodeSpecialCharacters($content)
    {
        return str_replace(['{amp}', '{comma}'], ['&', ';'], $content);
    }

    /**
     * Encode content
     *
     * @param string $content
     * @return string
     */
    public function encodeContent($content)
    {
        return htmlentities($content);
    }

    /**
     * Decode content
     *
     * @param string $content
     * @return string
     */
    public function decodeContent($content)
    {
        return html_entity_decode($content, ENT_QUOTES);
    }

    /**
     * Get category name
     *
     * @param string $item
     * @return string
     */
    public function getCategoryName($item)
    {
        $category = $this->getCategory($item['content_category']);
        if (!is_null($category)) {
            return $category->getName();
        }

        return '';
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
            $category = $this->getCategory($item['content_category']);
            // Parent Category
            if (!is_null($category)) {
                $categories = [
                    'category' => $this->getCategoryData($category),
                    'children' => []
                ];

                // Overwrite main category link if link is defined already for the menu item
                if (!empty($item['link'])) {
                    $categories['category']['link'] = $this->getItemLink($item);
                }

                // Remove main category link if it is defined separately
                if (!empty($item['remove_category_anchor'])) {
                    $categories['category']['link'] = '';
                }

                // Subcategories
                if ($this->isChildrenCategoriesVisible($item)) {
                    $subcategories = $category->getChildrenCategories();
                    foreach ($subcategories as $subcategory) {
                        $categories['children'][] = $this->getCategoryData($subcategory);
                    }
                }

                return $categories;
            }
        }

        return '';
    }

    /**
     * Get Category
     *
     * @param string $categoryIdPath
     * @return Category|null
     */
    protected function getCategory($categoryIdPath)
    {
        try {
            $contentCategory = explode('/', $categoryIdPath);
            if ($contentCategory && isset($contentCategory[self::OFFSET_CATEGORY_ID])) {
                $categoryId = $contentCategory[self::OFFSET_CATEGORY_ID];

                return $this->categoryRepository->get($categoryId);
            }
        } catch (NoSuchEntityException $error) {
            // Magento Category Repository throws NoSuchEntityException when no category found. That shouldn't break the FE rendering
            $this->_logger->error($error);
        }

        return null;
    }

    /**
     * Is children categories visible
     *
     * @param array $item
     * @return boolean
     */
    protected function isChildrenCategoriesVisible($item)
    {
        return $item['content_category_type'] === self::CATEGORY_TYPE_SHOW
            || $item['content_category_type'] === self::CATEGORY_TYPE_TOGGLE;
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
        $content = $this->decodeContent($content);
        $content = $this->decodeSpecialCharacters($content);

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
        if (!empty($item['link'])) {
            return $this->getDecodedContent($item['link']); // Also resolve magento url directives
        }

        return 'javascript:;'; // For opening submenu items, no links defined
    }
}
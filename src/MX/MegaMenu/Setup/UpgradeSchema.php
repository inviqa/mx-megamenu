<?php

namespace MX\MegaMenu\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const TABLE_MEGAMENU_ITEM = 'mx_megamenu_item';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCategoryTypeField($setup);
        }
    }

    /**
     * Add category type title
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function addCategoryTypeField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(self::TABLE_MEGAMENU_ITEM),
            'content_category_type',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'size' => 10,
                'after' => 'content_category',
                'comment' => 'Content Category Type'
            ]
        );
        return $this;
    }
}

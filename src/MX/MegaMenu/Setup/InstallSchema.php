<?php

namespace MX\MegaMenu\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    const TABLE_MEGAMENU = 'mx_megamenu';
    const TABLE_MEGAMENU_STORE = 'mx_megamenu_store';
    const TABLE_MEGAMENU_ITEM = 'mx_megamenu_item';
    const TABLE_STORE = 'store';

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists(self::TABLE_MEGAMENU)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(self::TABLE_MEGAMENU)
            )
                ->addColumn(
                    'menu_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Menu ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Menu Name'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Status'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At')
                ->setComment('Menu Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(self::TABLE_MEGAMENU_ITEM)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(self::TABLE_MEGAMENU_ITEM)
            )
                ->addColumn(
                    'menu_item_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Menu Item ID'
                )
                ->addColumn(
                    'menu_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu ID'
                )
                ->addColumn(
                    'menu_item_parent_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Parent ID'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Status'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Menu Item Name'
                )
                ->addColumn(
                    'link',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Menu Item Link'
                )
                ->addColumn(
                    'header_status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Header Status'
                )
                ->addColumn(
                    'header_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false
                    ],
                    'Menu Item Header Content'
                )
                ->addColumn(
                    'content_status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Main Content Status'
                )
                ->addColumn(
                    'content_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false
                    ],
                    'Menu Item Main Content Content'
                )
                ->addColumn(
                    'content_type',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Menu Item Content Type'
                )
                ->addColumn(
                    'content_category',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Menu Item Content Category'
                )
                ->addColumn(
                    'leftside_status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Left Side Status'
                )
                ->addColumn(
                    'leftside_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false
                    ],
                    'Menu Item Left Side Content'
                )
                ->addColumn(
                    'rightside_status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Right Side Status'
                )
                ->addColumn(
                    'rightside_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false
                    ],
                    'Menu Item Right Side Content'
                )
                ->addColumn(
                    'footer_status',
                    Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Footer Status'
                )
                ->addColumn(
                    'footer_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false
                    ],
                    'Menu Item Footer Content'
                )
                ->addColumn(
                    'sort_order',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu Item Sort Order'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At')
                ->addIndex(
                    $installer->getIdxName(self::TABLE_MEGAMENU_ITEM, ['menu_item_parent_id']),
                    ['menu_item_parent_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        self::TABLE_MEGAMENU_ITEM,
                        'menu_id',
                        self::TABLE_MEGAMENU,
                        'menu_id'
                    ),
                    'menu_id',
                    $installer->getTable(self::TABLE_MEGAMENU),
                    'menu_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Menu Item Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(self::TABLE_MEGAMENU_STORE)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(self::TABLE_MEGAMENU_STORE)
            )
                ->addColumn(
                    'menu_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Menu ID'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Store ID'
                )
                ->addIndex(
                    $installer->getIdxName(self::TABLE_MEGAMENU_STORE, ['menu_id']),
                    ['menu_id']
                )
                ->addIndex(
                    $installer->getIdxName(self::TABLE_MEGAMENU_STORE, ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        self::TABLE_MEGAMENU_STORE,
                        'menu_id',
                        self::TABLE_MEGAMENU,
                        'menu_id'
                    ),
                    'menu_id',
                    $installer->getTable(self::TABLE_MEGAMENU),
                    'menu_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        self::TABLE_MEGAMENU_STORE,
                        'store_id',
                        self::TABLE_STORE,
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable(self::TABLE_STORE),
                    'store_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Menu Stores Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
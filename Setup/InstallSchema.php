<?php
namespace Az2009\Cielo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->getConnection()->tableColumnExists('sales_order_payment', 'card_token')) {
            $installer->run("
                ALTER TABLE {$installer->getTable('sales_order_payment')} 
                  ADD COLUMN card_token VARCHAR(100) DEFAULT NULL"
            );
        }

        $installer->run("                  
                        ALTER TABLE {$installer->getTable('sales_order_payment')}
                          MODIFY COLUMN last_trans_id VARCHAR(100); 
                    ");

        $installer->endSetup();
    }
}

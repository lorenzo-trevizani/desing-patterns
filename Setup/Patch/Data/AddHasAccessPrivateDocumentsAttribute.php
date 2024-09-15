<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace CustomVendor\PDPGroupSections\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Data patch to add has_access_private_documents attribute to customer entity
 */
class AddHasAccessPrivateDocumentsAttribute implements DataPatchInterface
{
    /** @var string */
    public const HAS_ACCESS_PRIVATE_DOCUMENTS = 'has_access_private_documents';

    /**
     * @var Attribute $attributeResource
     */
    private $attributeResource;

    /**
     * @var Config $eavConfig
     */
    private $eavConfig;

    /**
     * @var EavSetupFactory $eavSetupFactory
     */
    private $eavSetupFactory;

   /**
    * @var ModuleDataSetupInterface $moduleDataSetup
    */
    private $moduleDataSetup;
 
    /**
     * CustomerAttribute Constructor
     * @param Attribute $attributeResource
     * @param Config $eavConfig
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        Attribute $attributeResource,
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->attributeResource = $attributeResource;
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::HAS_ACCESS_PRIVATE_DOCUMENTS,
            [
                'type' => 'int',
                'label' => 'Access To Private Documents',
                'input' => 'boolean',
                'required' => false,
                'default' => '0',
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 999,
                'position' => 999,
                'system' => 0,
            ]
        );
 
        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);
 
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::HAS_ACCESS_PRIVATE_DOCUMENTS);
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);
 
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
 
        $this->attributeResource->save($attribute);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}

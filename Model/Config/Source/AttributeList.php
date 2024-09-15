<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\Model\Config\Source;

use CustomVendor\PDPGroupSections\Helper\AttributesSelected as AttributesHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

class AttributeList implements ArrayInterface
{
    /**
     * @var AttributesHelper
     */
    protected AttributesHelper $attributesHelper;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $eavAttributesCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param AttributesHelper $attributesHelper
     * @param CollectionFactory $eavAttributesCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        AttributesHelper $attributesHelper,
        CollectionFactory $eavAttributesCollectionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->attributesHelper = $attributesHelper;
        $this->eavAttributesCollectionFactory = $eavAttributesCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get all attrubutes array into option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $arr = $this->_toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /**
     * Get all attrubutes into array [id => label]
     *
     * @return array
     */
    protected function _toArray(): array
    {
        $attributes = $this->getEavAttributesCollectionBySection();
        $attributesList = array();

        foreach ($attributes as $attribute) {
            $attributesList[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        return $attributesList;
    }

    /**
     * Get eav attributes collection by section
     *
     * @return Collection
     */
    protected function getEavAttributesCollectionBySection(): Collection
    {
        return $this->getEavAttributesCollection();
    }

    /**
     * Get eav attributes collection
     *
     * @param string $xmlPath
     * @return Collection
     */
    protected function getEavAttributesCollection(string $xmlPath = ''): Collection
    {
        $collection = $this->eavAttributesCollectionFactory->create();

        if ($xmlPath != '') {
            //Add filter to get only attributes that are used in the selected section
            $collection->getSelect()
            ->join(
                ['eave' => $collection->getTable('eav_entity_attribute')],
                'eave.attribute_id = main_table.attribute_id',
                []
            )
            ->join(
                ['eavg' => $collection->getTable('eav_attribute_group')],
                'eavg.attribute_group_id = eave.attribute_group_id',
                []
            )
            ->where('eavg.attribute_group_code LIKE ?', '%' . $this->attributesHelper->getConfigValue($xmlPath) . '%')
            ->group('eave.attribute_id');
        }

        return $collection;
    }
}

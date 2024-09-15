<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace CustomVendor\PDPGroupSections\ViewModel;

use CustomVendor\PDPGroupSections\Helper\AttributesSelected as AttributesHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Phrase;

class SeparateAttributesViewModel implements ArgumentInterface
{
    /**
     * @var AttributesHelper
     */
    protected AttributesHelper $attributesHelper;

    /**
     * SeparateAttributesViewModel constructor.
     *
     * @param AttributesHelper $attributesHelper
     */
    public function __construct(
        AttributesHelper $attributesHelper
    ) {
        $this->attributesHelper = $attributesHelper;
    }

    public function getDistributedAttributes(array $allAttr): array
    {
        $attributesGroups = [
            'general' => [
                'label'=> __('General'),
                'attributes' => []
            ],
            'electrical' => [
                'label'=> __('Electrical'),
                'attributes' => []
            ],
            'physical' => [
                'label'=> __('Physical'),
                'attributes' => []
            ],
            'additional_info' => [
                'label'=> __('Additional Info'),
                'attributes' => []
            ]
        ];

        $electricalList = $this->attributesHelper->getElectricalAttributeArray();
        $physicalList = $this->attributesHelper->getPhysicalAttributeArray();
        $additionalInfoList = $this->attributesHelper->getAdditionalInfoAttributeArray();

        foreach ($allAttr as $code => $attr) {
            switch (true) {
                case in_array($code, $electricalList):
                    $attributesGroups['electrical']['attributes'][$code] = $attr;
                    break;
                case in_array($code, $physicalList):
                    $attributesGroups['physical']['attributes'][$code] = $attr;
                    break;
                case in_array($code, $additionalInfoList):
                    $attributesGroups['additional_info']['attributes'][$code] = $attr;
                    break;
                default:
                    $attributesGroups['general']['attributes'][$code] = $attr;
            }
        }

        return $attributesGroups;
    }
}

<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\Plugin\Block\Product\View;

use CustomVendor\PDPGroupSections\Helper\AttributesSelected as AttributesHelper;
use Magento\Catalog\Block\Product\View\Attributes;

/**
 * Class AttributesPlugin.
 *
 * Plugin for Product Attribute class
 */
class AttributesPlugin
{
    /**
     * @var AttributesHelper
     */
    protected AttributesHelper $attributesHelper;

    /**
     * AttributesPlugin constructor.
     *
     * @param AttributesHelper $attributesHelper
     */
    public function __construct(
        AttributesHelper $attributesHelper
    ) {
        $this->attributesHelper = $attributesHelper;
    }

    /**
     * Filter attributes that are already used on other product's group section
     * so that they do not return on the getAdditionalData method, used for main general section.
     *
     * @param Attributes $subject
     * @param array $result
     * @param array $excludeAttr
     * @return array
     */
    public function afterGetAdditionalData(Attributes $subject, array $result, array $excludeAttr = [])
    {
        $excludedAttrFromSections = $this->attributesHelper->getAllSelectedAttributesFromSections();

        if (!empty($excludedAttrFromSections)) {
            foreach ($excludedAttrFromSections as $attribute_code) {
                unset($result[$attribute_code]);
            }
        }

        return $result;
    }
}

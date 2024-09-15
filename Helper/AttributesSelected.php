<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\Helper;

use CustomVendor\PDPGroupSections\Block\Product\View\Attributes;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class AttributesSelected
 * @package CustomVendor\PDPGroupSections\Helper
 */
class AttributesSelected extends AbstractHelper
{
    /**
     * Get selected attributes from path
     *
     * @return array
     */
    public function getAllSelectedAttributesFromSections() : array
    {
        return array_merge(
            $this->getSelectedAttributes(Attributes::XML_PATH_FEATURE_ATTRIBUTE),
            $this->getSelectedAttributes(Attributes::XML_PATH_PACKAGING_ATTRIBUTE),
            $this->getSelectedAttributes(Attributes::XML_PATH_COMPLIANCE_ATTRIBUTE)
        );
    }

    /**
     * Get selected attributes from path.
     *
     * @param string $xmlPath
     * @return array
     */
    public function getSelectedAttributes($xmlPath): array
    {
        $attributes = [];

        if ($attributesConfig = $this->getConfigValue($xmlPath)) {
            $attributes = explode(',', $attributesConfig);
        }

        return $attributes;
    }

    /**
     * Get the list of attribute code for Electrical Group
     *
     * @return array
     */
    public function getElectricalAttributeArray(): array
    {
        return [
            'watts',
            'hid_equivalent',
            'volts',
            'operating_frequency',
            'cct',
            'color_temp',
            'cri',
            'lumens',
            'beam_angle',
            'dimmable',
            'dimming_type',
            'hours_rated',
            'operating_temp'
        ];
    }

    /**
     * Get the list of attribute code for Physical Group
     *
     * @return array
     */
    public function getPhysicalAttributeArray(): array
    {
        return [
            'mol',
            'mod_lamps',
            'fixture_shape',
            'lamp_type',
            'lamp_base',
            'ansi_base',
            'lamp_finish',
            'housing_color',
            'weight'
        ];
    }

    /**
     * Get the list of attribute code for Additional Info Group
     *
     * @return array
     */
    public function getAdditionalInfoAttributeArray(): array
    {
        return [
            'installation_notes',
            'rated_for_enclosed_fixture',
            'warranty'
        ];
    }

    /**
     * Get the config value from store configuration
     *
     * @param string $xmlPath
     * @return string
     */
    public function getConfigValue(string $xmlPath, $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $xmlPath,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        ) ?? '';
    }
}

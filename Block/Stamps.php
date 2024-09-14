<?php

/**
 * Certification Stamps' Product Attribute
 *
 * @package CustomVendor_CertificationStamps
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\CertificationStamps\Block;

use CustomVendor\CertificationStamps\Block\Adminhtml\Form\Field\AttributeMapping;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Data as CatalogHelperData;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Helper\Media as SwatchesHelperMedia;
use Magento\Swatches\Helper\Data as SwatchesHelperData;

class Stamps extends Template
{
    /** Attribute Code for Certification Stamps @var string */
    protected const CERTIFICATION_STAMPS_ATTRIBUTE_CODE = 'certification_stamps';
    /** Swatch Image Url Key in Attribute @var string */
    protected const SWATCH_IMAGE_URL_KEY = 'swatch_image_url';
    /** Row number key for Attribute @var string */
    protected const ROW_ATTRIBUTE_KEY = 'row_number';
    /** XML path for attribute separator  @var string */
    protected const XML_PATH_ATTRIBUTE_SEPARATOR = 'custom_vendor_certification_stamps/general/attribute_separator';
    /** XML path for enabled multirow configuration @var string */
    protected const XML_PATH_IS_MULTIROW_ENABLED = 'custom_vendor_certification_stamps/general/enable_multi_row';
    /** XML path for attribute mapping @var string */
    protected const XML_PATH_ATTRIBUTE_MAPPING = 'custom_vendor_certification_stamps/general/attribute_mapping';
    /** Attribute Type for Product Attributes @var string */
    protected const ATTRIBUTE_TYPE = 'catalog_product';
    /** Acceptance value for not empty attribute @var string */
    protected const ACCEPTANCE_VALUE_NOT_EMPTY = 'not_empty';

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var CatalogHelperData
     */
    protected CatalogHelperData $catalogHelperMedia;

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var SwatchesHelperMedia
     */
    protected SwatchesHelperMedia $swatchHelperMedia;

    /**
     * @var SwatchesHelperData
     */
    protected SwatchesHelperData $swatchHelperData;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param CatalogHelperData $catalogHelperMedia
     * @param Config $eavConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param SwatchesHelperMedia $swatchHelperMedia
     * @param SwatchesHelperData $swatchHelperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogHelperData $catalogHelperMedia,
        Config $eavConfig,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        SwatchesHelperMedia $swatchHelperMedia,
        SwatchesHelperData $swatchHelperData,
        array $data
    ) {
        $this->catalogHelperMedia = $catalogHelperMedia;
        $this->eavConfig = $eavConfig;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->swatchHelperMedia = $swatchHelperMedia;
        $this->swatchHelperData = $swatchHelperData;
        parent::__construct($context, $data);
    }

    /**
     * Get product stamps depending on multirow configuration
     *
     * @return array
     */
    public function getProductStamps(): array
    {
        $allProductStamps = $this->getAllProductStamps();
        $stamps = [];
        
        if ($this->isMultiRowEnabled()) {
            foreach ($allProductStamps as $stamp) {
                $rowNumber = $stamp[self::ROW_ATTRIBUTE_KEY] ?: '1';
                $stamps[$rowNumber][] = $stamp;
            }

            // Sort each group by their original keys
            ksort($stamps);
        } else {
            $stamps[] = $allProductStamps;
        }

        return $stamps;
    }

    /**
     * Get an array with product's certification stamps by row number
     *
     * @param int $rowNumber. Default value is 1
     * @return array
     */
    public function getProductStampsByRow(int $rowNumber = 1): array
    {
        $allProductStamps = $this->getAllProductStamps();
        $rowStamps = [];

        foreach ($allProductStamps as $stamp) {
            if ($stamp[self::ROW_ATTRIBUTE_KEY] == strval($rowNumber)) {
                array_push($rowStamps, $stamp);
            }
        }

        return $rowStamps;
    }

    /**
     * Get an array with all product stamps and populate them with their swatch image
     *
     * @return array
     */
    public function getAllProductStamps(): array
    {
        $options = $this->getAllSwatchOptions();
        $mediaUrl = $this->swatchHelperMedia->getSwatchMediaUrl();
        $options = $this->filterSwatchOptions($options);

        foreach ($options as $key => $option) {
            if (!empty($option['value']) && !empty($option['label'])) {
                $optionValue = $option['value'];
                $options[$key][self::ROW_ATTRIBUTE_KEY] = $this->getSwatchImageRow($option['label']);
                $swatchImage = $this->swatchHelperData->getSwatchesByOptionsId([$optionValue]);

                if (!empty($swatchImage) && array_key_exists($optionValue, $swatchImage) && isset($swatchImage[$optionValue]['value'])) {
                    $options[$key][self::SWATCH_IMAGE_URL_KEY] = $mediaUrl . $swatchImage[$optionValue]['value'];
                }
            }
        }

        return $options;
    }

    /**
     * Filter swatch option according to the product attribute value
     *
     * @return array
     */
    public function filterSwatchOptions($options): array
    {
        $product = $this->catalogHelperMedia->getProduct();
        $filteredOptions = [];

        foreach ($options as $option) {
            $eavAttribute = $this->eavConfig->getAttribute(self::ATTRIBUTE_TYPE, $this->getRelatedAttributeCode($option['label']));
            $attributeValue = '';
            $acceptanceValue = $this->getAcceptanceValue($option['label']);

            if ($eavAttribute && $eavAttribute->getFrontend()) {
                $value = $eavAttribute->getFrontend()->getValue($product);
                $attributeValue = $value ? strval($value) : '';
            }

            if ($this->isAttributeValueValid($attributeValue, $acceptanceValue)) {
                array_push($filteredOptions, $option);
            }
        }

        return $filteredOptions;
    }

    /**
     * Is attribute value valid according to acceptance value
     *
     * @param string $attributeValue
     * @param string $acceptanceValue
     * @return bool
     */
    public function isAttributeValueValid(string $attributeValue, string $acceptanceValue): bool
    {
        $acceptanceValue = strtolower($acceptanceValue);
        $attributeValue = strtolower($attributeValue);
        $isValid = ($acceptanceValue == $attributeValue);
        $attributeMappingArray = $this->getAttributeMappingArray();

        // Check if there is mapping for acceptance value
        if (!$isValid && array_key_exists($acceptanceValue, $attributeMappingArray)) {
            $valuesMap = $attributeMappingArray[$acceptanceValue];
            $isSingleValue = count($valuesMap) == 1;

            if (!$isSingleValue) {
                return in_array($attributeValue, $valuesMap);
            }

            return $attributeValue == $valuesMap[0] || stripos($attributeValue, $valuesMap[0]) !== false;
        }

        // A preexisting acceptance value to check if there is value in attribute
        if ($acceptanceValue == self::ACCEPTANCE_VALUE_NOT_EMPTY && !empty($attributeValue)) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * Get attribute mapping array
     *
     * @return array
     */
    public function getAttributeMappingArray(): array
    {
        $encodedAttributeMapping = $this->getConfigValue(self::XML_PATH_ATTRIBUTE_MAPPING);

        if (!$encodedAttributeMapping) {
            return [];
        }

        $attributeMapping = $this->serializer->unserialize($encodedAttributeMapping);
        $attributeMappingArray = [];

        if ($attributeMapping) {
            foreach ($attributeMapping as $mapping) {
                $arrayValues = [];

                if (!array_key_exists(strtolower($mapping[AttributeMapping::ACCEPTANCE_VALUE_KEY]), $attributeMappingArray)) {
                    $arrayValues = explode(',', $mapping[AttributeMapping::ATTRIBUTE_MAPPING_KEY]);
                    $arrayValues = array_map('strtolower', $arrayValues);
                    $attributeMappingArray[strtolower($mapping[AttributeMapping::ACCEPTANCE_VALUE_KEY])] = $arrayValues;
                }
            }
        }

        return $attributeMappingArray;
    }

    /**
     * Get all swatch options array from attribute
     *
     * @return array
     */
    public function getAllSwatchOptions(): array
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', self::CERTIFICATION_STAMPS_ATTRIBUTE_CODE);
        $options = [];
        if ($attribute->usesSource()) {
            $options = $this->cleanOptionsArray($attribute->getSource()->getAllOptions());
        }
        return $options;
    }

    /**
     * Clean option array to remove empty or null values
     *
     * @param array $options
     * @return array $options
     */
    protected function cleanOptionsArray(array $options): array
    {
        $incompleteProcess = true;
        $option = array_column($options, 'value');
        $escape_safety = false;

        while ($incompleteProcess) {
            /**
             * The while's escape is the $found_key return, it will be either
             * the key value or false if there is no empty values.
             */
            $found_key = array_search('', $option);
            $optionLen = count($option);

            if ($found_key === false || $escape_safety == true) {
                $incompleteProcess = false;
            } else {
                unset($option[$found_key]);
                unset($options[$found_key]);
            }

            /**
             * This is a fallback safety approach to prevet infinity loop:
             * $escape_safety will be true in case the loop run without removing
             * any empty value from the array.
             */
            $escape_safety = ($optionLen == count($option));
        }

        return $options;
    }

    /**
     * Get attribute code from swatch attribute code
     *
     * @param string $swatchAttributeCode
     * @return string $attributeCode
     */
    public function getRelatedAttributeCode(string $swatchAttributeCode): string
    {
        $attributeCode = explode($this->getAttributeSeparator(), $swatchAttributeCode)[0];

        return $attributeCode;
    }

    /**
     * Get acceptance value from swatch attribute code to show swatch image
     * Default value is string ''
     *
     * @param string $swatchAttributeCode
     * @return string $acceptanceValue
     */
    public function getAcceptanceValue(string $swatchAttributeCode): string
    {
        $explodedSwatchAttributeCode = explode($this->getAttributeSeparator(), $swatchAttributeCode);
        $acceptanceValue = '';

        if (count($explodedSwatchAttributeCode) > 1) {
            $acceptanceValue = $explodedSwatchAttributeCode[1];
        }

        return $acceptanceValue;
    }

    /**
     * Get swatch image row value from swatch attribute code
     * Default value is (string) '1'
     *
     * @param string $swatchAttributeCode
     * @return string $swatchImageRow
     */
    public function getSwatchImageRow(string $swatchAttributeCode): string
    {
        $explodedSwatchAttributeCode = explode($this->getAttributeSeparator(), $swatchAttributeCode);
        $swatchImageRow = '1';

        if (count($explodedSwatchAttributeCode) > 2) {
            $swatchImageRow = $explodedSwatchAttributeCode[2];
        }

        return $swatchImageRow;
    }

    /**
     * Get the attribute separator value from xml path
     *
     * @return string|null
     */
    public function getAttributeSeparator($storeId = null): ?string
    {
        return $this->getConfigValue(self::XML_PATH_ATTRIBUTE_SEPARATOR, $storeId) ?? '^';
    }

    /**
     * Is multirow enabled on configuration
     *
     * @param string $attributeValue
     * @param string $acceptanceValue
     * @return bool
     */
    public function isMultiRowEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue(self::XML_PATH_IS_MULTIROW_ENABLED, $storeId) ?? 0;
    }

    /**
     * Get SWATCH_IMAGE_URL_KEY const value
     *
     * @return string
     */
    public function getSwatchImageUrlkey(): string
    {
        return self::SWATCH_IMAGE_URL_KEY;
    }

    /**
     * Get the config value from store configuration
     *
     * @param string $xmlPath
     * @return string|null
     */
    public function getConfigValue(string $xmlPath, $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            $xmlPath,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }
}

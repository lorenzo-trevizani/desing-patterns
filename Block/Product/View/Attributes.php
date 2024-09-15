<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace CustomVendor\PDPGroupSections\Block\Product\View;

use CustomVendor\PDPGroupSections\Helper\AttributesSelected as AttributesHelper;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Attributes block
 */
class Attributes extends Template
{
    /** PDF Files attribute code @var string */
    public const PDF_FILE_ATTRIBUTE_CODE = 'pdf_files';
    /** XML path for feature attribute @var string */
    public const XML_PATH_FEATURE_ATTRIBUTE = 'CustomVendor_pdp_group_sections/feature_section/attributes';
    /** XML path for packaging attribute @var string */
    public const XML_PATH_PACKAGING_ATTRIBUTE = 'CustomVendor_pdp_group_sections/packaging_section/attributes';
    /** XML path for compliance attribute @var string */
    public const XML_PATH_COMPLIANCE_ATTRIBUTE = 'CustomVendor_pdp_group_sections/compliance_section/attributes';

    /**
     * @var AttributesHelper
     */
    protected AttributesHelper $attributesHelper;

    /**
     * @var Data
     */
    protected Data $catalogHelperData;

    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @param AttributesHelper $attributesHelper
     * @param Data $catalogHelperData
     * @param Config $eavConfig
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        AttributesHelper $attributesHelper,
        Data $catalogHelperData,
        Config $eavConfig,
        Context $context,
        array $data
    ) {
        $this->attributesHelper = $attributesHelper;
        $this->catalogHelperData = $catalogHelperData;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get all product attributes assigned to section
     *
     * @param string $xmlPath
     * @return array
     */
    public function getAllSectionAttributes(string $xmlPath = ''): array
    {
        $data = [];
        $product = $this->getProduct();

        if (!$product) {
            return $data;
        }

        $allowedAttributes = $this->attributesHelper->getConfigValue($xmlPath);
        $allowedAttributes = $allowedAttributes ? explode(',', $allowedAttributes) : [];

        foreach ($allowedAttributes as $attributeCode) {
            if ($product->getData($attributeCode) && !array_key_exists($attributeCode, $data)) {
                $eavAttribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);

                if ($eavAttribute) {
                    $data[$attributeCode] = [
                        'label' => $eavAttribute->getFrontendLabel(),
                        'value' => $product->getData($attributeCode),
                        'code' => $eavAttribute->getAttributeCode(),
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Returns a Product
     *
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->catalogHelperData->getProduct();
    }
}

<?php

/**
 * Certification Stamps' Product Attribute
 *
 * @package CustomVendor_CertificationStamps
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\CertificationStamps\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class AttributeMapping
 * @package CustomVendor_CertificationStamps
 */
class AttributeMapping extends AbstractFieldArray
{
    /** Acceptance value key of field @var string */
    public const ACCEPTANCE_VALUE_KEY = 'acceptance_value';
    /** Acceptance value key of field @var string */
    public const ATTRIBUTE_MAPPING_KEY = 'attribute_mapping';

    /**
     * Prepare rendering the new field by adding all the needed columns
     * @return void
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            self::ACCEPTANCE_VALUE_KEY,
            [
                'label' => __('Acceptance Value of Attribute'),
                'class' => 'required-entry'
            ]
        );
        $this->addColumn(
            self::ATTRIBUTE_MAPPING_KEY,
            [
                'label' => __('Attribute Mapping Values'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }
}

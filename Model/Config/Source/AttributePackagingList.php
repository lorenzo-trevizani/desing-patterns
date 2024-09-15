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

use CustomVendor\PDPGroupSections\Model\Config\Source\AttributeList;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;

class AttributePackagingList extends AttributeList
{
    /** XML path for packaging attribute @var string */
    public const XML_PATH_PACKAGING_ATTRIBUTE_GROUP_FILTER = 'CustomVendor_pdp_group_sections/packaging_section/attributes_group';

    /**
     * Get eav attributes collection by section with xml param
     *
     * @return Collection
     */
    protected function getEavAttributesCollectionBySection(): Collection
    {
        return $this->getEavAttributesCollection(self::XML_PATH_PACKAGING_ATTRIBUTE_GROUP_FILTER);
    }
}

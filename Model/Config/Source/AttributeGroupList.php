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

use Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Option\ArrayInterface;

class AttributeGroupList implements ArrayInterface
{
    /** Attribute group code for filter attributes @var string */
    public const ATTRIBUTE_GROUP_CODE = 'pim-data';

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductAttributeGroupRepositoryInterface
     */
    protected ProductAttributeGroupRepositoryInterface $productAttributeGroupRepository;

    /**
     * Constructor
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductAttributeGroupRepositoryInterface $productAttributeGroupRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductAttributeGroupRepositoryInterface $productAttributeGroupRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productAttributeGroupRepository = $productAttributeGroupRepository;
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
    private function _toArray(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $productAttributeGroup = $this->productAttributeGroupRepository->getList($searchCriteria)->getItems();
        $attributesGroupList = array();

        foreach ($productAttributeGroup as $group) {
            $attributesGroupList[$group->getAttributeGroupCode()] = $group->getAttributeGroupName();
        }

        return $attributesGroupList;
    }
}

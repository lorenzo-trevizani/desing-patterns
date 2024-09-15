<?php
/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
namespace CustomVendor\PDPGroupSections\Block;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class ProductSpecs extends Template
{
    protected $productFactory;
    protected $registry;
    protected $product;

    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    public function getProduct(): ?ProductInterface
    {
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        return $this->product;
    }
}

<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\ViewModel;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductDetails implements ArgumentInterface
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * Constructor.
     * @param Image $imageHelper
     */
    public function __construct(
        Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get image URL for the product.
     * @param Product $product
     * @return string
     */
    public function getImageUrl(Product $product): string
    {
        return $this->imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getSmallImage())
            ->resize(300)
            ->getUrl();
    }
}

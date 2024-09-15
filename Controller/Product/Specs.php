<?php

/**
 * PDP Group Sections
 *
 * @package CustomVendor_PDPGroupSections
 * @author Lorenzo Trevizani <lm.trevizani@gmail.com>
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\Controller\Product;

use CustomVendor\PDPGroupSections\Service\GenerateProductPdfService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mpdf\Output\Destination as PdfDestination;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Layout;

class Specs implements HttpGetActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private GenerateProductPdfService $generateProductPdfService,
        private FileFactory $fileFactory,
        private LayoutFactory $layoutFactory,
        private ProductRepositoryInterface $productRepository,
        private Registry $registry
    ) {
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(): ResponseInterface
    {
        $productId = $this->request->getParam('id');
        if (!$productId) {
            throw new \Exception('Product ID is required');
        }

        $this->loadProductToRegistry();
        $product = $this->registry->registry('current_product');
        $productSku = $product->getSku();

        /** @var Layout $layoutEntity */
        $layoutEntity = $this->layoutFactory->create(['cacheable' => false]);
        $layoutEntity->getUpdate()->load('catalog_product_specs');
        $layoutEntity->generateXml()->generateElements();

        $content = $layoutEntity->getOutput();

        $filename = sprintf("%s Specifications.pdf", $productSku);
        $pdf = $this->generateProductPdfService->generatePdf($content, $filename);

        return $this->fileFactory->create(
            $filename,
            $pdf->Output(
                $filename,
                PdfDestination::STRING_RETURN
            ),
            DirectoryList::TMP,
            'application/pdf'
        );
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    private function loadProductToRegistry(): void
    {
        $productId = $this->request->getParam('id');
        $product = $this->productRepository->getById($productId);
        $this->registry->register('current_product', $product);
        $this->registry->register('product', $product);
    }
}

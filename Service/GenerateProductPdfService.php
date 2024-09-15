<?php

declare(strict_types=1);

namespace CustomVendor\PDPGroupSections\Service;

use Magento\Framework\Filesystem\DirectoryList;
use Mpdf\Mpdf;

class GenerateProductPdfService
{
    public function __construct(
        private DirectoryList $directoryList
    ) {
    }

    public function generatePdf(string $content, string $filename): Mpdf
    {
        $pdf = new Mpdf($this->getPdfConfig());
        $pdf->WriteHTML($content);
        $pdf->Output($filename, \Mpdf\Output\Destination::INLINE);

        return $pdf;
    }

    private function getPdfConfig(): array
    {
        // add the PDF settings required
        // @see https://mpdf.github.io/reference/mpdf-variables/overview.html

        return [
            'tempDir' => $this->directoryList->getPath('tmp'),
            'curlAllowUnsafeSslRequests' => true,
            'format' => [216, 279.4],
            'mode' => 'utf-8',
            'margin_left' => 2.5,
            'margin_top' => 2,
            'margin_right' => 2.5,
            'margin_header' => 0,
            'margin_footer' => 2,
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\BarcodeLabelTemplate;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Exception;

class BarcodeService
{
    private BarcodeGeneratorPNG $barcodeGenerator;

    public function __construct()
    {
        $this->barcodeGenerator = new BarcodeGeneratorPNG();
    }

    /**
     * Generate barcode PNG for a product
     */
    public function generateProductBarcode(Product $product, string $type = 'EAN13'): string
    {
        $barcodeValue = $product->sku ?? $product->barcode ?? $product->id;

        try {
            return $this->barcodeGenerator->getBarcode($barcodeValue, $this->getBarcodeType($type));
        } catch (Exception $e) {
            // Fallback: return base64 encoded empty image or throw
            throw new Exception("Failed to generate barcode: " . $e->getMessage());
        }
    }

    /**
     * Generate QR code PNG
     */
    public function generateQRCode(string $data, int $size = 200): string
    {
        try {
            $qrCode = new QrCode($data);
            $qrCode->setSize($size);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            return $result->getString();
        } catch (Exception $e) {
            throw new Exception("Failed to generate QR code: " . $e->getMessage());
        }
    }

    /**
     * Generate label HTML for printing
     */
    public function generateLabelHTML(Product $product, BarcodeLabelTemplate $template): string
    {
        $barcodeData = base64_encode($this->generateProductBarcode($product));
        $elements = $template->elements ?? $this->getDefaultElements();

        $html = '<div style="width: ' . $template->width_mm . 'mm; height: ' . $template->height_mm . 'mm; position: relative; overflow: hidden;">';

        foreach ($elements as $element) {
            $html .= $this->renderElement($element, $product, $barcodeData);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a single label element
     */
    private function renderElement(array $element, Product $product, string $barcodeData): string
    {
        $type = $element['type'] ?? 'text';
        $x = $element['x'] ?? 0;
        $y = $element['y'] ?? 0;
        $width = $element['width'] ?? 50;
        $height = $element['height'] ?? 10;
        $fontSize = $element['font_size'] ?? 10;
        $align = $element['align'] ?? 'left';

        $style = "position: absolute; left: {$x}mm; top: {$y}mm; width: {$width}mm; height: {$height}mm; text-align: {$align};";

        switch ($type) {
            case 'barcode':
                return "<div style=\"{$style}\"><img src=\"data:image/png;base64,{$barcodeData}\" style=\"width: 100%; height: auto;\"/></div>";

            case 'qrcode':
                $qrData = $element['data'] ?? url('/products/' . $product->id);
                $qrCode = base64_encode($this->generateQRCode($qrData, 150));
                return "<div style=\"{$style}\"><img src=\"data:image/png;base64,{$qrCode}\" style=\"width: 100%; height: auto;\"/></div>";

            case 'product_name':
                return "<div style=\"{$style} font-size: {$fontSize}pt; font-weight: bold;\">" . htmlspecialchars($product->name) . "</div>";

            case 'price':
                $price = $this->formatPrice($product->default_price);
                return "<div style=\"{$style} font-size: {$fontSize}pt;\">Rp {$price}</div>";

            case 'sku':
                return "<div style=\"{$style} font-size: {$fontSize}pt;\">SKU: " . htmlspecialchars($product->sku ?? 'N/A') . "</div>";

            case 'barcode_text':
                return "<div style=\"{$style} font-size: {$fontSize}pt; font-family: monospace;\">" . htmlspecialchars($product->barcode ?? $product->sku ?? '') . "</div>";

            default:
                $content = $element['content'] ?? '';
                return "<div style=\"{$style} font-size: {$fontSize}pt;\">" . htmlspecialchars($content) . "</div>";
        }
    }

    /**
     * Get default label elements
     */
    private function getDefaultElements(): array
    {
        return [
            ['type' => 'product_name', 'x' => 2, 'y' => 2, 'width' => 46, 'height' => 8, 'font_size' => 10],
            ['type' => 'barcode', 'x' => 2, 'y' => 12, 'width' => 46, 'height' => 20],
            ['type' => 'price', 'x' => 2, 'y' => 34, 'width' => 46, 'height' => 8, 'font_size' => 12],
        ];
    }

    /**
     * Map barcode type string to constant
     */
    private function getBarcodeType(string $type): int
    {
        return match ($type) {
            'EAN13' => $this->barcodeGenerator::TYPE_EAN_13,
            'EAN8' => $this->barcodeGenerator::TYPE_EAN_8,
            'UPCA' => $this->barcodeGenerator::TYPE_UPC_A,
            'CODE128' => $this->barcodeGenerator::TYPE_CODE_128,
            'CODE39' => $this->barcodeGenerator::TYPE_CODE_39,
            default => $this->barcodeGenerator::TYPE_EAN_13,
        };
    }

    /**
     * Format price with Indonesian Rupiah
     */
    private function formatPrice(float $price): string
    {
        return number_format($price, 0, ',', '.');
    }

    /**
     * Generate bulk labels HTML for multiple products
     */
    public function generateBulkLabels(array $products, BarcodeLabelTemplate $template): string
    {
        $html = '<style>
            @media print {
                body { margin: 0; }
                .label-page { page-break-after: always; }
                .no-break { page-break-inside: avoid; }
            }
            .label-container { display: flex; flex-wrap: wrap; gap: 2mm; }
        </style>';

        $html .= '<div class="label-container">';

        foreach ($products as $product) {
            $html .= '<div class="no-break">';
            $html .= $this->generateLabelHTML($product, $template);
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Validate barcode data
     */
    public function validateBarcode(string $barcode, string $type = 'EAN13'): bool
    {
        return match ($type) {
            'EAN13' => $this->isValidEAN13($barcode),
            'EAN8' => $this->isValidEAN8($barcode),
            'UPCA' => $this->isValidUPCA($barcode),
            default => true,
        };
    }

    /**
     * Validate EAN-13 barcode
     */
    private function isValidEAN13(string $barcode): bool
    {
        if (strlen($barcode) !== 13 || !ctype_digit($barcode)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $barcode[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int) $barcode[12];
    }

    /**
     * Validate EAN-8 barcode
     */
    private function isValidEAN8(string $barcode): bool
    {
        if (strlen($barcode) !== 8 || !ctype_digit($barcode)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += (int) $barcode[$i] * ($i % 2 === 0 ? 3 : 1);
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int) $barcode[7];
    }

    /**
     * Validate UPC-A barcode
     */
    private function isValidUPCA(string $barcode): bool
    {
        if (strlen($barcode) !== 12 || !ctype_digit($barcode)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $sum += (int) $barcode[$i] * ($i % 2 === 0 ? 3 : 1);
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int) $barcode[11];
    }
}

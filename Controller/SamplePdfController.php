<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

class SamplePdfController extends BasePdfController
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        ob_start();
        extract([
            'charset'     => 'utf-8',
        ]);
        require_once (dirname(__DIR__) . '/Resources/views/sample.php');

        return ob_get_clean();
    }
}
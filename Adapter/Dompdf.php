<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Adapter;

use Dompdf\Dompdf as BaseDompdf;
use Dompdf\Options;
use tiFy\Plugins\Pdf\Contracts\Adapter;

class Dompdf extends AbstractAdapter
{
    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaults(): array
    {
        return [
            'args'     => [
                'charset'     => get_bloginfo('charset') ?: 'utf-8',
                'stylesheets' => []
            ],
            'filename' => 'file.pdf',
            'html'     => function (...$args) {
                return (string)$this->app->viewer('template::pdf/pdf', compact('args'));
            },
            'pdf'      => [
                'base_path'   => PUBLIC_PATH,
                'orientation' => 'portrait',
                'size'        => 'A4',
                'options'     => [
                    'isPhpEnabled' => true
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function driver()
    {
        if (is_null()) {
            $this->driver = new Dompdf();
        }

        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    protected function generate(): Adapter
    {
        set_time_limit(0);

        $html = is_callable($this->params('html'))
            ? call_user_func_array($this->params('html'), $this->params('args', []))
            : $this->params('html');

        $this->driver()->loadHtml($html);
        $this->driver()->render();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        $this->generate();

        return $this->driver()->output();
    }

    /**
     * @inheritDoc
     */
    public function pdfStream()
    {
        $string = $this->output();
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }

    /**
     * Définition de la liste des options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if ($options = $this->params('options', [])) {
            $this->pdf->setOptions(new Options($options));
        }

        if ($basePath = $this->params('base_path')) {
            $this->pdf->setBasePath($basePath);
        }

        if ($this->params('size') || $this->params('orientation')) {
            $size = $this->params('size', 'A4');
            $orientation = $this->params('orientation', 'portrait');
            $this->pdf->setPaper($size, $orientation);
        }

        return $this;
    }
}
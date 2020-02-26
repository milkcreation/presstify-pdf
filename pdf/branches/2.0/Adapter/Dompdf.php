<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Adapter;

use Dompdf\Dompdf as BaseDompdf;
use Dompdf\Options;
use tiFy\Plugins\Pdf\Contracts\Adapter;

/**
 * @see https://github.com/dompdf/dompdf/wiki/Usage
 */
class Dompdf extends AbstractAdapter
{
    /**
     * {@inheritDoc}
     *
     * @return BaseDompdf
     */
    public function driver(): BaseDompdf
    {
        if (is_null($this->driver)) {
            $this->driver = new BaseDompdf();
        }

        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function generate(): Adapter
    {
        set_time_limit(0);

        $html = $this->controller->html();

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
    public function setConfig(array $params): Adapter
    {
        if ($options = $params['options'] ?? []) {
            $this->driver()->setOptions(new Options($options));
        }

        if ($basePath = $params['base_path'] ?? '') {
            $this->driver()->setBasePath($basePath);
        }

        if (isset($params['size']) || isset($params['orientation'])) {
            $size = $params['size'] ?? 'A4';
            $orientation = $params['orientation'] ?? 'portrait';
            $this->driver()->setPaper($size, $orientation);
        }

        return $this;
    }
}
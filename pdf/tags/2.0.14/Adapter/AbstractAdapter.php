<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Adapter;

use tiFy\Plugins\Pdf\Contracts\Adapter;
use tiFy\Plugins\Pdf\Contracts\PdfController;

abstract class AbstractAdapter implements Adapter
{
    /**
     * Instance du controleur associé.
     * @var PdfController
     */
    protected $controller;

    /**
     * Instance du pilote de génération de PDF.
     * @var mixed
     */
    protected $driver;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->output();
    }

    /**
     * @inheritDoc
     */
    public function generate(): Adapter
    {
        return $this->driver();
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        $this->generate();

        return '';
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $params): Adapter
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setController(PdfController $controller): Adapter
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stream()
    {
        $string = $this->output();
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }
}
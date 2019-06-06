<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Plugins\Pdf\Adapter\Dompdf;
use tiFy\Plugins\Pdf\Contracts\Adapter;
use tiFy\Plugins\Pdf\Contracts\Controller;
use tiFy\Support\ParamsBag;

abstract class AbstractPdfController implements Controller
{
    /**
     * Instance du générateur de PDF.
     * @var Adapter
     */
    protected $adapter;

    /**
     * Instance du gestionnaire d'injection de dépendance.
     * @var Container|null
     */
    protected $container;

    /**
     * Instance du gestionnaire de paramètres de configuration.
     * @var ParamsBag
     */
    protected $params;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container Instance du gestionnaire d'injection de dépendance.
     *
     * @return void
     */
    public function __construct(?Container $container)
    {
        $this->container = $container;

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function adapter() : Adapter
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->params = ParamsBag::createFromAttrs($this->defaults());
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'filename' => 'file.pdf',
            'pdf'      => [
                'driver'      => 'dompdf',
                'base_path'   => PUBLIC_PATH,
                'orientation' => 'portrait',
                'size'        => 'A4',
                'options'     => [
                    'isPhpEnabled' => true
                ]
            ],
            'storage'  => false,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->params;
        } elseif (is_array($key)) {
            return $this->params->set($key);
        } else {
            return $this->params->get($key, $default);
        }
    }

    /**
     * @inheritDoc
     */
    public function parse(...$args): Controller
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function response($disposition = 'inline'): StreamedResponse
    {
        $response = new StreamedResponse();
        $disposition = $response->headers->makeDisposition($disposition, $this->params('filename'));
        $response->headers->replace([
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition,
        ]);
        $response->setCallback(function () {
            $stream = $this->setAdapter()->adapter()->stream();
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function responseDisplay(...$args): StreamedResponse
    {
        return $this->parse(...$args)->response('inline');
    }

    /**
     * @inheritDoc
     */
    public function responseDownload(...$args): StreamedResponse
    {
        return $this->parse(...$args)->response('attachment');
    }

    /**
     * @inheritDoc
     */
    public function responseHtml(...$args): string
    {
        return $this->parse(...$args)->getContent();
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(?Adapter $adapter = null): Controller
    {
        if ($adapter) {
            $this->adapter = $adapter;
        } elseif($container = $this->getContainer()) {
            $alias = $this->params()->pull('pdf.driver', 'dompdf');
            $this->adapter = $container->has("pdf.adapter.{$alias}")
                ? $container->get("pdf.adapter.{$alias}") : $container->get(Adapter::class);
        } else {
            switch($this->params()->pull('pdf.driver', 'dompdf')) {
                default :
                case 'dompdf' :
                    $this->adapter = new Dompdf();
                    break;
            }
        }

        $this->adapter->setController($this)->setConfig($this->params('pdf', []));

        return $this;
    }
}
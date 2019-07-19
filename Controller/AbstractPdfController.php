<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Filesystem\StorageManager;
use tiFy\Plugins\Pdf\{Adapter\Dompdf, Contracts\Adapter, Contracts\Controller};
use tiFy\Support\ParamsBag;

abstract class AbstractPdfController extends ParamsBag implements Controller
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
     * Instance du gestionnaire de stockage des fichiers.
     * @var LocalFilesystem|null|false
     */
    protected $storage;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container Instance du gestionnaire d'injection de dépendance.
     *
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function adapter(): Adapter
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {

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
                    'isPhpEnabled' => true,
                ],
            ],
            'storage'  => false,
            'renew'    => false
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
    public function getFilename(): string
    {
        return $this->get('filename');
    }

    /**
     * @inheritDoc
     */
    public function parseArgs(...$args): Controller
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function response($disposition = 'inline'): StreamedResponse
    {
        set_time_limit(0);

        $this->parse()->setAdapter();

        $response = new StreamedResponse();
        $disposition = $response->headers->makeDisposition($disposition, $this->getFilename());
        $response->headers->replace([
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition,
        ]);
        $response->setCallback(function () {
            $stream = $this->storage() ? $this->store() : $this->adapter()->stream();

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
        return $this->parseArgs(...$args)->response('inline');
    }

    /**
     * @inheritDoc
     */
    public function responseDownload(...$args): StreamedResponse
    {
        return $this->parseArgs(...$args)->response('attachment');
    }

    /**
     * @inheritDoc
     */
    public function responseHtml(...$args): string
    {
        return $this->parseArgs(...$args)->parse()->getContent();
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(?Adapter $adapter = null): Controller
    {
        if ($adapter) {
            $this->adapter = $adapter;
        } elseif ($container = $this->getContainer()) {
            $alias = $this->pull('pdf.driver', 'dompdf');
            $this->adapter = $container->has("pdf.adapter.{$alias}")
                ? $container->get("pdf.adapter.{$alias}") : $container->get(Adapter::class);
        } else {
            switch ($this->pull('pdf.driver', 'dompdf')) {
                default :
                case 'dompdf' :
                    $this->adapter = new Dompdf();
                    break;
            }
        }

        $this->adapter->setController($this)->setConfig($this->pull('pdf', []));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renew(): bool
    {
        return (bool)$this->get('renew', false);
    }

    /**
     * @inheritDoc
     */
    public function storage(): ?LocalFilesystem
    {
        if (is_null($this->storage)) {
            $storage = $this->get('storage');

            if (is_string($storage)) {
                $manager = $this->getContainer() ? $this->getContainer()->get('storage') : new StorageManager();
                $storage = $manager->localFilesytem($storage);
            }

            $this->storage = ($storage instanceof LocalFilesystem)  ? $storage : null;
        }

        return $this->storage;
    }

    /**
     * @inheritDoc
     */
    public function store()
    {
        if ($storage = $this->storage()) {
            if (!$storage->has($this->getFilename()) || $this->renew()) {
                $storage->putStream($this->getFilename(), $this->adapter()->stream());
            }

            return $this->storage()->readStream($this->getFilename());
        }
    }
}
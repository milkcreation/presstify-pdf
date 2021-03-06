<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

use Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Http\Response;
use tiFy\Filesystem\StorageManager;
use tiFy\Plugins\Pdf\Adapter\Dompdf;
use tiFy\Plugins\Pdf\Contracts\Adapter;
use tiFy\Plugins\Pdf\Contracts\PdfController;
use tiFy\Routing\BaseController;
use tiFy\Support\Filesystem as fs;
use tiFy\Support\Proxy\Url;

class BasePdfController extends BaseController implements PdfController
{
    /**
     * Instance du générateur de PDF.
     * @var Adapter
     */
    protected $adapter;

    /**
     * Chemin racine.
     * @var string|null
     */
    private $base;

    /**
     * Instance du gestionnaire de stockage des fichiers.
     * @var LocalFilesystem|null|false
     */
    protected $storage;

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
    public function defaults(): array
    {
        return [
            'filename' => 'file.pdf',
            'handle'   => function (PdfController $controller, ...$args) {
                return null;
            },
            'html'     => function (PdfController $controller) {
                return '';
            },
            'pdf'      => [
                'driver'      => 'dompdf',
                'base_path'   => ROOT_PATH,
                'orientation' => 'portrait',
                'size'        => 'A4',
                'options'     => [
                    'isPhpEnabled'         => true,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => true,
                ],
            ],
            'renew'    => false,
            'storage'  => false,
        ];
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
    public function handle(...$args): PdfController
    {
        $this->parse();

        if (is_callable(($handler = $this->get('handle', null)))) {
            $handler($this, ...$args);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return is_callable($html = $this->get('html', '')) ? $html($this) : $html;
    }

    /**
     * @inheritDoc
     */
    public function parse(): PdfController
    {
        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function path(string $rel): string
    {
        return fs::normalizePath($this->base . '/' . $rel);
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
    public function responseDefault($disposition = 'inline'): StreamedResponse
    {
        if (is_null($this->base)) {
            $this->setBase(ROOT_PATH);
        }

        $this->setAdapter();

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
        return $this->handle(...$args)->responseDefault('inline');
    }

    /**
     * @inheritDoc
     */
    public function responseDownload(...$args): StreamedResponse
    {
        return $this->handle(...$args)->responseDefault('attachment');
    }

    /**
     * @inheritDoc
     */
    public function responseHtml(...$args): Response
    {
        if (is_null($this->base)) {
            $this->setBase(Url::scope());
        }

        return $this->response($this->handle(...$args)->html());
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(?Adapter $adapter = null): PdfController
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
    public function setBase(string $base): PdfController
    {
        $this->base = $base;

        return $this;
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

            $this->storage = ($storage instanceof LocalFilesystem) ? $storage : null;
        }

        return $this->storage;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function store()
    {
        if ($storage = $this->storage()) {
            if (!$storage->has($this->getFilename()) || $this->renew()) {
                $storage->putStream($this->getFilename(), $this->adapter()->stream());
            }

            return $this->storage()->readStream($this->getFilename());
        }

        return null;
    }
}
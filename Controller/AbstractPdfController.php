<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Support\ParamsBag;

abstract class AbstractPdfController
{
    /**
     * Instance du gestionnaire de paramètres de configuration.
     * @var ParamsBag
     */
    protected $params;

    /**
     * Instance de la librairie de génération de PDF.
     * @var Dompdf
     */
    protected $pdf;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->params = ParamsBag::createFromAttrs($this->defaults());
    }

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaults()
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
     * Définition ou récupération de l'instance ou d'attributs de configuration.
     *
     * @param string|array|null $key Indice de qualification|Liste des attributs à définir.
     * @param mixed $default Valeur de retour par défaut lorsque l'indice de récupération d'un attribut est défini.
     *
     * @return ParamsBag|mixed
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
     * Génération du PDF.
     *
     * @return Dompdf
     */
    protected function pdfGenerate(): Dompdf
    {
        set_time_limit(0);

        $html = is_callable($this->params('html'))
            ? call_user_func_array($this->params('html'), $this->params('args', []))
            : $this->params('html');

        $this->pdf->loadHtml($html);
        $this->pdf->render();

        return $this->pdf;
    }

    /**
     * Récupération de la sortie d'affichage du PDF.
     *
     * @return string
     */
    public function pdfOutput(): string
    {
        $this->pdfGenerate();

        return $this->pdf->output();
    }

    /**
     * Récupération de la sortie stream du PDF.
     *
     * @return resource
     */
    public function pdfStream()
    {
        $string = $this->pdfOutput();
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }

    /**
     * Récupération de la reponse HTTP.
     *
     * @param string $disposition inline|attachment.
     *
     * @return StreamedResponse
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
            $stream = $this->setPdf()->pdfStream();
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Affichage du PDF
     *
     * @return mixed
     */
    public function responseDisplay(): StreamedResponse
    {
        $this->params(request()->input('params', []));

        return $this->response('inline');
    }

    /**
     * Téléchargement du PDF.
     *
     * @return StreamedResponse
     */
    public function responseDownload(): StreamedResponse
    {
        $this->params(request()->input('params', []));

        return $this->response('attachment');
    }

    /**
     * Définition de l'instance du générateur de PDF.
     *
     * @param Dompdf $dompdf Instance du générateur de PDF
     *
     * @return Pdf
     */
    public function setPdf(?Dompdf $dompdf = null): self
    {
        $this->pdf = $dompdf ?: new Dompdf();

        if ($options = $this->params('pdf.options', [])) {
            $this->pdf->setOptions(new Options($options));
        }

        if ($basePath = $this->params('pdf.base_path')) {
            $this->pdf->setBasePath($basePath);
        }

        if ($this->params('pdf.size') || $this->params('pdf.orientation')) {
            $size = $this->params('pdf.size', 'A4');
            $orientation = $this->params('pdf.orientation', 'portrait');
            $this->pdf->setPaper($size, $orientation);
        }

        return $this;
    }
}
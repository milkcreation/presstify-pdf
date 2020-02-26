<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Http\Response;
use tiFy\Routing\BaseController;

/**
 * @mixin BaseController
 */
interface PdfController
{
    /**
     * Récupération de l'instance du générateur de PDF.
     *
     * @return Adapter
     */
    public function adapter(): Adapter;

    /**
     * Récupération du nom de qualification du fichier PDF.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Traitement d'une liste de variable passées en argument.
     *
     * @param array ...$args
     *
     * @return static
     */
    public function handle(...$args): PdfController;

    /**
     * Récupération de la sortie HTML du PDF.
     *
     * @return string
     */
    public function html(): string;

    /**
     * Récupération de l'état de demande de renouvellement du fichier stocké.
     *
     * @return boolean
     */
    public function renew(): bool;

    /**
     * Récupération de la reponse HTTP par défaut.
     *
     * @param string $disposition inline|attachment.
     *
     * @return StreamedResponse
     */
    public function responseDefault($disposition = 'inline'): StreamedResponse;

    /**
     * Affichage du PDF.
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return mixed
     */
    public function responseDisplay(...$args): StreamedResponse;

    /**
     * Téléchargement du PDF.
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return StreamedResponse
     */
    public function responseDownload(...$args): StreamedResponse;

    /**
     * Affichage du HTML
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return Response
     */
    public function responseHtml(...$args): Response;

    /**
     * Définition de l'instance du générateur de PDF.
     *
     * @param Adapter $adapter Instance du générateur de PDF
     *
     * @return static
     */
    public function setAdapter(?Adapter $adapter = null): PdfController;

    /**
     * Récupération de l'instance du gestionnaire de stockage du fichier.
     *
     * @return LocalFilesystem|null
     */
    public function storage(): ?LocalFilesystem;

    /**
     * Stockage du fichier dans le répertoire de dépôt.
     *
     * @return mixed
     */
    public function store();
}
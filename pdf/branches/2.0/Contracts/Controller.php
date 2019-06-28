<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Contracts;

use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Filesystem\StorageManager;

interface Controller extends ParamsBag
{
    /**
     * Récupération de l'instance du générateur de PDF.
     *
     * @return Adapter
     */
    public function adapter(): Adapter;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaults(): array;

    /**
     * Récupération du contenu du PDF.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Récupération du controleur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

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
    public function parseArgs(...$args): Controller;

    /**
     * Récupération de la reponse HTTP.
     *
     * @param string $disposition inline|attachment.
     *
     * @return StreamedResponse
     */
    public function response($disposition = 'inline'): StreamedResponse;

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
     * @return string
     */
    public function responseHtml(...$args): string;

    /**
     * Définition de l'instance du générateur de PDF.
     *
     * @param Adapter $adapter Instance du générateur de PDF
     *
     * @return static
     */
    public function setAdapter(?Adapter $adapter = null): Controller;

    /**
     * Récupération de l'état de demande de renouvellement du fichier stocké.
     *
     * @return boolean
     */
    public function renew(): bool;

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
<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Contracts;

use Psr\Container\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Support\ParamsBag;

interface Controller
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
     * Définition ou récupération de l'instance ou d'attributs de configuration.
     *
     * @param string|array|null $key Indice de qualification|Liste des attributs à définir.
     * @param mixed $default Valeur de retour par défaut lorsque l'indice de récupération d'un attribut est défini.
     *
     * @return ParamsBag|mixed
     */
    public function params($key = null, $default = null);

    /**
     * Traitement d'une liste de variable passées en argument.
     *
     * @param array ...$args
     *
     * @return static
     */
    public function parse(...$args): Controller;

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
}
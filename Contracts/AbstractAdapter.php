<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Adapter
{
    /**
     * Résolution de la classe sous la forme d'un chaine de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaults(): array;

    /**
     * Récupération du pilote génération de PDF.
     *
     * @return mixed
     */
    public function driver();

    /**
     * Génération du PDF.
     *
     * @return static
     */
    public function generate(): Adapter;

    /**
     * Récupération de la sortie d'affichage du PDF.
     *
     * @return string
     */
    public function output(): string;

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
     * Définition de la liste des options
     *
     * @param array $options
     *
     * @return static
     */
    public function setOptions(array $options): Adapter;

    /**
     * Récupération de la sortie stream du PDF.
     *
     * @return resource
     */
    public function stream();
}
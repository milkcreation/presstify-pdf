<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Contracts;

interface Adapter
{
    /**
     * Résolution de la classe sous la forme d'un chaine de caractère.
     *
     * @return string
     */
    public function __toString(): string;

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
     * Définition de la liste des options
     *
     * @param array $params Liste des paramètres de configuration.
     *
     * @return static
     */
    public function setConfig(array $params): Adapter;

    /**
     * Définition du controleur associé.
     *
     * @param Controller $controller
     *
     * @return Adapter
     */
    public function setController(Controller $controller): Adapter;

    /**
     * Récupération de la sortie stream du PDF.
     *
     * @return resource
     */
    public function stream();
}
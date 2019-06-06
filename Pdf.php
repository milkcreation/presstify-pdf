<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf;

use Psr\Container\ContainerInterface;

/**
 * Class Pdf
 *
 * @desc Extension PresstiFy de génération de PDF.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Pdf
 * @version 2.0.0
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Pdf\PdfServiceProvider à la liste des fournisseurs de services.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Pdf\PdfServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          Pdf\PdfServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier pdf.php
 * @see Resources/config/pdf.php
 */
class Pdf
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}

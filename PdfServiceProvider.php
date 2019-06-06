<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf;

use tiFy\Container\ServiceProvider;

class PdfServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'pdf'
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function() {
            $this->getContainer()->get('pdf');
        });
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share('pdf', function() {
            return new Pdf($this->getContainer());
        });
    }
}
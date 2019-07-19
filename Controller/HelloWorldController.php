<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Controller;

class HelloWorldController extends AbstractPdfController
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'args'     => [
                'charset'     => get_bloginfo('charset') ?: 'utf-8',
                'stylesheets' => []
            ]
        ]);
    }

    /**
     * Récupération du controleur d'injection de dépendances.
     *
     * @return string
     */
    public function getContent(): string
    {
        ob_start();
        extract($this->get('args', []));
        require_once (dirname(__DIR__) . '/Resources/views/hello-world.php');

        return ob_get_clean();
    }
}
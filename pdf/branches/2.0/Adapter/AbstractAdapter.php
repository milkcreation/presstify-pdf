<?php declare(strict_types=1);

namespace tiFy\Plugins\Pdf\Adapter;

use tiFy\Plugins\Pdf\Contracts\Adapter;
use tiFy\Support\ParamsBag;

abstract class AbstractAdapter implements Adapter
{
    /**
     * Instance du pilote de génération de PDF
     * @var mixed
     */
    protected $driver;

    /**
     * Résolution de la classe sous la forme d'un chaine de caractère.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->output();
    }

    /**
     * @inheritDoc
     */
    public function generate(): Adapter
    {
        set_time_limit(0);

        return $this->driver();
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        $this->generate();

        return '';
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
     * @inheritDoc
     */
    public function setOptions(array $options): Adapter
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stream()
    {
        $string = $this->output();
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }
}
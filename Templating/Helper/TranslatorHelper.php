<?php

namespace Knp\Bundle\TranslatorBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper as BaseTranslatorHelper;

/**
 * TranslatorHelper.
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class TranslatorHelper extends BaseTranslatorHelper
{
    protected $config;

    public function __construct(TranslatorInterface $translator, $config) {
        parent::__construct($translator);
        $this->config = $config;
    }

    /**
     * @see TranslatorInterface::trans()
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        if (!isset($locale)) {
            $locale = $this->translator->getLocale();
        }


        $trans = parent::trans($id, $parameters, $domain, $locale);

        return $this->wrap($id, $trans, 'trans', $domain, $locale);
    }

    /**
     * @see TranslatorInterface::transChoice()
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        if (!isset($locale)) {
            $locale = $this->translator->getLocale();
        }

        $trans = parent::transChoice($id, $number, $parameters, $domain, $locale);

        return $this->wrap($id, $trans, 'trans_choice', $domain, $locale);
    }

    /**
     * Wraps a translated value with e.g. <span data-translation='test_translation_id'> This is a test translation </span>
     * Used to detect in-line edition of translations
     *
     * @return string
     */
    public function wrap($id, $trans, $type, $domain = 'messages', $locale = null)
    {
        $attr = $this->config['attr'];

        if (!isset($locale)) {
            $locale = $this->translator->getLocale();
        }

        $attr[$this->config['keys']['id']] = $id;
        $attr[$this->config['keys']['domain']] = $domain;
        $attr[$this->config['keys']['locale']] = $locale;
        $attr[$this->config['keys']['type']] = $type;

        $attributes = "";
        foreach($attr as $key => $value) {
            $attributes .= " ". $key . "=" . "\"" . $value . "\"";
        }

        return new \Twig_Markup(sprintf('<%s %s>%s</%s>', $this->config['tag'], $attributes, $trans, $this->config['tag']), 'utf-8');
    }
}

<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Knp\Bundle\TranslatorBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Bundle\TranslatorBundle\Translation\Writer;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorController
{
    private $translator;
    private $writer;
    private $logger;

    public function __construct(TranslatorInterface $translator, Writer $writer, $logger)
    {
        $this->translator = $translator;
        $this->writer = $writer;
        $this->logger = $logger;
    }

    public function getAction($id, $domain, $locale)
    {
        $trans = array(
            'id' => $id,
            'domain' => $domain,
            'locale',
            'translation' => $this->translator->trans($id, array(), $domain, $locale)
        );

        return new JsonResponse($trans);
    }

    public function putAction(Request $request)
    {
        $id = $request->get('id');
        $value = $request->get('value');

        //do not allow empty translations, as one cannot edit them again
        if($value == "") {
            $value = "_";
        }
        $domain = $request->get('domain');
        $locale = $request->get('locale');
        $this->writer->write($id, $value, $domain, $locale);

        $translation = array(
            'id' => $id,
            'domain' => $domain,
            'locale' => $locale,
            'translation' => $this->translator->trans($id, array(), $domain, $locale)
        );

        return new JsonResponse($translation);
    }
}

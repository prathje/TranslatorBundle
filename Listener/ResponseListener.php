<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\TranslatorBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;

/**
 * ResponseListener injects the translator js code.
 *
 * The handle method must be connected to the onCoreResponse event.
 *
 * The js is only injected on well-formed HTML (with a proper </body> tag).
 *
 */
class ResponseListener
{
    protected $assetHelper;
    protected $routeprotected;
    protected $spanningElConfig;

    public function __construct(AssetsHelper $assetHelper, RouterInterface $router, $spanningElConfig)
    {
        $this->assetHelper = $assetHelper;
        $this->router = $router;
        $this->spanningElConfig = $spanningElConfig;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $this->injectScripts($response);
        $this->injectCss($response);
    }

    /**
     * Injects the js scripts into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectScripts(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();

        if (false !== $pos = $posrFunction($content, '</body>')) {

            $scripts = '';
            $url = $this->assetHelper->getUrl('bundles/knptranslator/js/jquery-2.1.4.min.js');
            $scripts = sprintf('<script type="text/javascript" src="%s"></script>', $url)."\n";

            $url = $this->assetHelper->getUrl('bundles/knptranslator/js/translator.js');
            $scripts .= sprintf('<script type="text/javascript" src="%s"></script>', $url)."\n";


            $script= <<<HTML
<script type="text/javascript">
    var knpTranslator = null;
    $(function() {
        knpTranslator = new Knp.Translator($, '%s', '%s');
    });
</script>
HTML;
            $scripts .= sprintf($script, $this->router->generate('knp_translator_put'), json_encode($this->spanningElConfig))."\n";

            $content = $substrFunction($content, 0, $pos).$scripts.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * Injects the css links into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectCss(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();

        if (false !== $pos = $posrFunction($content, '</head>')) {

            $url = $this->assetHelper->getUrl('bundles/knptranslator/css/translator.css');
            $links = sprintf('<link rel="stylesheet" href="%s" />', $url);

            $content = $substrFunction($content, 0, $pos).$links.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}

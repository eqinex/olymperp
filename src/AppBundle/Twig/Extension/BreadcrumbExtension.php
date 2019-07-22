<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\Routing\Router;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbExtension extends \Twig_Extension
{
    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param Breadcrumbs $breadcrumbs
     * @param Router $router
     */
    public function __construct(Breadcrumbs $breadcrumbs, Router $router)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('breadcrumb', array($this, 'addBreadcrumb'))
        );
    }

    public function addBreadcrumb($label, $url = '', array $translationParameters = array())
    {
        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'breadcrumb_extension';
    }
}
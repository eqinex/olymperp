<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'navbar-nav mr-auto',
            ],
        ]);
//
//        $menu
//            ->addChild('goods.requests.own', ['route' => 'goodsrequest_index'])
//            ->setAttribute('class', 'nav-item')
//            ->setLinkAttribute('class', 'nav-link');
//
//        $menu
//            ->addChild('goods.requests.need_approve', ['route' => 'goodsrequest_need_approve_list'])
//            ->setAttribute('class', 'nav-item')
//            ->setLinkAttribute('class', 'nav-link');

        return $menu;
    }
}
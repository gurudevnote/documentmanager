<?php
// src/AppBundle/Menu/Builder.php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('navbar-header');
        $menu['navbar-header']->addChild('Home', array('route' => 'homepage'));

        $menu->addChild('navbar-collapse');
        $menu['navbar-collapse']->addChild('navbar-nav');
        $menu['navbar-collapse']['navbar-nav']->addChild('Documents', array('route' => 'documents'));
        $menu['navbar-collapse']['navbar-nav']->addChild('Folders', array('route' => 'folders'));


        // create another menu item
        $menu['navbar-collapse']['navbar-nav']->addChild('About Me', array('route' => 'fos_user_profile_show'));
        // you can also add sub level's to your menu's as follows
        $menu['navbar-collapse']['navbar-nav']['About Me']->addChild('Edit profile', array('route' => 'fos_user_profile_edit'));

        $menu['navbar-collapse']->addChild('navbar-right');
        $menu['navbar-collapse']['navbar-right']->addChild('Administrator', array('route' => 'admin_homepage'));

        return $menu;
    }
}
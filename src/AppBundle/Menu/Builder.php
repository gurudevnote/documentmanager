<?php
// src/AppBundle/Menu/Builder.php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function navbarMenu(FactoryInterface $factory, array $options)
    {
        $authorizationChecker = $this->container->get('security.authorization_checker');
        $menu = $factory->createItem('root');
        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('Documents', array('route' => 'documents'));
        $menu->addChild('Folders', array('route' => 'folders'));

        // create another menu item
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild('About Me', array('uri' => '#'));
            // you can also add sub level's to your menu's as follows
            $menu['About Me']->addChild('My profile', array('route' => 'fos_user_profile_show'));
            $menu['About Me']->addChild('Edit profile', array('route' => 'fos_user_profile_edit'));
        }
        return $menu;
    }
}
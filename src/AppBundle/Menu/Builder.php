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
        return $menu;
    }

    public function rightMenu(FactoryInterface $factory, array $options)
    {
        $authorizationChecker = $this->container->get('security.authorization_checker');
        $menu = $factory->createItem('root');

        // create another menu item
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') || $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $mc = $this->container->get('p5notification.messagecenter');
            $menu->addChild('notification', array('extras'=>array('number_notification'=>$mc->getNotificationNumber())));
            $messages = $mc->getNotifications();
            if(count($messages) > 0){
                foreach($messages as $value){
                    $menu['notification']->addChild($value->getMessage()->getContent(), array('uri' => '#'));
                }
            }

            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $menu->addChild('Administrator', array('route'=>'admin_homepage'));
            }
            $avatar = $user->getAvatar();
            if($avatar == null || $avatar == '') {
                $avatar =  $this->container->get('templating.helper.assets')->getUrl('bundles/app/images/avatarDefault.png');
            }
            $menu->addChild($user->getEmail(), array('uri' => '#'));
            $menu->addChild('avatar', array('uri'=> $avatar));
            $menu['avatar']->addChild('My profile', array('route' => 'fos_user_profile_show'));
            $menu['avatar']->addChild('Edit profile', array('route' => 'fos_user_profile_edit'));
            $menu['avatar']->addChild('Logout', array('route' => 'fos_user_security_logout'));
        }
        else{
            $menu->addChild('Login', array('route'=>'fos_user_security_login'));
        }

        return $menu;
    }
}
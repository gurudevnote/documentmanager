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
            $messages = $mc->getNotifications();
            $notifications = array();
            if(count($messages) > 0){
                foreach($messages as $value){
                    $notifications[] = $value->getMessage()->getContent();
                }
            }
            $menu->addChild('notification', array('extras'=>array('number_notification'=>$mc->getNotificationNumber(), 'subitems'=>$notifications)));


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

    public function leftMenu(FactoryInterface $factory, array $options) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->container->get('doctrine')->getManager();
        $folderRepository = $em->getRepository('P5:Folder');
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.root, f.lft', 'ASC');
        $folders = $query->getQuery()->getResult();

        $menu = $factory->createItem('root');
        $menu->addChild('My docs', ['route' => 'documents']);
        foreach($folders as $key => $folder) {
            $parentName[$folder->getLvl()] = $folder->getName();
            $parentMenu = $menu['My docs'];
            if ($folder->getParent() != null) {
                for ($i = 0; $i < $folder->getLvl(); $i++) {
                    $parentMenu = $parentMenu[$parentName[$i]];
                }
            }

            $parentMenu->addChild($folder->getName(), ['extras' => ['lvl' => $folder->getLvl()], 'route' => 'fos_user_profile_show']);
        }
        $menu->addChild('Share with me', ['route' => 'list_shared_documents']);

        return $menu;
    }
}
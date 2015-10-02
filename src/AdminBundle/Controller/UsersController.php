<?php


namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller {
    /**
     * @Route("/user", name="admin_user_list")
     * @Template()
     */
    public function listAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return array('users' => $users);
    }
}
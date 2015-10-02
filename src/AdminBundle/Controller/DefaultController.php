<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="admin_homepage")
     *
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    public function menuAction()
    {
        return $this->render('AdminBundle:Default:menu.html.twig');
    }
}

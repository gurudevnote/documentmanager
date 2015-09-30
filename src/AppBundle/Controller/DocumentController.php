<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * @Route("/document", name="document")
     */
    public function indexAction(Request $request)
    {
        return new Response('Document page');
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * Default menu.
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $folderRepository = $em->getRepository('P5:Folder');
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('f.root, f.lft', 'ASC');

        $options = array(
            'decorate' => true,
            'rootOpen' => function ($tree) {
                $class = 'nav-second-level';
                if ($tree['0']['lvl'] == 1) {
                    $class = 'nav-third-level';
                }
                if ($tree['0']['lvl'] == 2) {
                    $class = 'nav-fourth-level';
                }
                if ($tree['0']['lvl'] == 3) {
                    $class = 'nav-fifth-level';
                }

                return '<ul class="nav '.$class.'">';
            },
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function ($node) {
                $html = '<a href="/documents/'.$node['id'].'">'.$node['name'];
                if (count($node['__children']) > 0) {
                    $html .= '<span class="fa arrow"></span></a>';
                } else {
                    $html .= '</a>';
                }

                return $html;
            },
        );
        $folders = $query->getQuery()->getArrayResult();
//        die(var_dump($folders));
        $foldersHtml = $folderRepository->buildTree($folders, $options);
//        $foldersHtml = $folderRepository->childrenHierarchy(null, false, $options);
        return $this->render('@App/default/menu.html.twig', [
            'foldersHtml' => $foldersHtml,
        ]);
    }
}

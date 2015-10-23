<?php

namespace AppBundle\Controller;

use Proxies\__CG__\P5\Model\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FolderController extends Controller
{
    /**
     * @Route("/folders", name="folders")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $folderRepository = $em->getRepository("P5:Folder");
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->orderBy('f.root, f.lft', 'ASC');
        $folders = $query->getQuery()->getResult();

        $folder = new Folder();
        $form = $this->createFormBuilder($folder)
            ->add('name', 'text')
            ->add('parent', 'entity', array('choices' => $folders, 'class' => 'P5\Model\Folder', 'property' => 'nameHierarchy', 'placeholder' => '--Choose a folder--'))
            ->add('save', 'submit', array('label' => 'Upload', 'attr'=>array('class'=>'btn-primary')))
            ->setAction($this->generateUrl('folders'))
            ->getForm();
        $form->handleRequest($request);
        if($form->isValid()){

            $folder->setUser($this->getUser());
            $folder->setUploadDate(new \DateTime());
            $folder->setLastModified(new \DateTime());

            $this->get('doctrine.orm.entity_manager')->persist($folder);
            $this->get('doctrine.orm.entity_manager')->flush();

            $messageCenter = $this->get('p5notification.messagecenter');
            $messageCenter->pushMessage($this->getUser(), 'A new folder was uploaded by ' . $this->getUser()->getEmail(), 'folder');

            return $this->redirect($this->generateUrl('folders'));
        }
        return array(
            'folders' => $folders,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/folder-list", name="folder_list")
     * @Template()
     */
    public function listAction()
    {
        return array();
    }
}

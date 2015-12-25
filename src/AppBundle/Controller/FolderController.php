<?php

namespace AppBundle\Controller;

use P5\Model\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FolderController extends Controller
{
    /**
     * @Route("/folders", name="folders")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $folderRepository = $em->getRepository('P5:Folder');
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user')
            ->orderBy('f.root, f.lft', 'ASC');
        $query->setParameter('user', $this->getUser());
        $folders = $query->getQuery()->getResult();

        return array(
            'folders' => $folders,
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

    /**
     * @Route("add-folder", name="add_folder")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $folderRepository = $em->getRepository('P5:Folder');
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user')
            ->orderBy('f.root, f.lft', 'ASC');
        $query->setParameter('user', $this->getUser());
        $folders = $query->getQuery()->getResult();

        $folder = new Folder();
        $form = $this->createFormBuilder($folder)
            ->add('name', 'text')
            ->add('parent', 'entity', array('choices' => $folders, 'class' => 'P5\Model\Folder', 'property' => 'nameHierarchy', 'placeholder' => '--Choose a folder--'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $folder->setUser($this->getUser());
            $folder->setUploadDate(new \DateTime());
            $folder->setLastModified(new \DateTime());

            $this->get('doctrine.orm.entity_manager')->persist($folder);
            $this->get('doctrine.orm.entity_manager')->flush();

            $messageCenter = $this->get('p5notification.messagecenter');
            $messageCenter->pushMessage($this->getUser(), 'A new folder was uploaded by '.$this->getUser()->getEmail(), 'folder', array('folder_id' => $folder->getId()));
            $this->get('session')->getFlashBag()->add('success', 'The folder is created successfully!');

            return new Response('<script language="JavaScript">parent.location.href="'.$this->generateUrl('folders').'"</script>');
        }

        return array(
            'form' => $form->createView(),
        );
    }
}

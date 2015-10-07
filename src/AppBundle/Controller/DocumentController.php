<?php

namespace AppBundle\Controller;

use P5\Model\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class DocumentController extends Controller
{
    /**
     * @Route("/documents", name="documents")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $documentRepository = $em->getRepository("P5:Document");
        $folderRepository = $em->getRepository("P5:Folder");

        $folders = $folderRepository->findAll();
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('filename', 'text')
            ->add('folder', 'entity', array('choices' => $folders, 'class' => 'P5\Model\Folder', 'property' => 'name', 'placeholder' => '--Choose a folder--'))
            ->add('save', 'submit', array('label' => 'Upload', 'attr'=>array('class'=>'btn-primary')))
            ->setAction($this->generateUrl('documents'))
            ->getForm();
        $form->handleRequest($request);
        if($form->isValid()){

            $document->setUser($this->getUser());
            $document->setFolder($folderRepository->find($document->getFolder()));
            $document->setUploadDate(new \DateTime());
            $document->setLastModified(new \DateTime());

            $this->get('doctrine.orm.entity_manager')->persist($document);
            $this->get('doctrine.orm.entity_manager')->flush();

            return $this->redirect($this->generateUrl('documents'));
        }

        $documents = $documentRepository->getMyDocuments($this->getUser());
        $authors = $documentRepository->getAllAuthors();
        $folders = $documentRepository->getAllFolders();

        return array(
            'documents' => $documents,
            'uploadForm' => $form->createView(),
            'authors' => $authors,
            'folders' => $folders,
        );
    }

    /**
     * @var int $id
     * @return link
     * @Route("/{id}/sharing", name="document_sharing")
     * @Template()
     */
    public function sharingAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository("P5:User");
        $documentRepository = $em->getRepository("P5:Document");

        $sharingForm = $this->createFormBuilder()
            ->add('user', 'entity', array(
                'class' => 'P5:User',
                'property'     => 'username',
                'multiple'     => true
            ))
            ->add('docId', 'hidden')
            ->getForm();
        $sharingForm->handleRequest($request);
        if ($sharingForm->isValid()) {
            $data = $request->request->get('form');
            $doc = $documentRepository->find($data['docId']);
            foreach($data['user'] as $value) {
                $user = $userRepository->find($value);
                if (!$doc->hasSharingUsers($user)) {
                    $doc->getSharingUsers()->add($user);
                }
            }
            $em->persist($doc);
            $em->flush();

            return $this->redirect($this->generateUrl('documents'));
        }

        return array(
            'sharingForm' => $sharingForm->createView(),
        );
    }

    /**
     * @Route("/document/upload", name="upload_document")
     */
    public function saveAction(Request $request){
        $folderRepository = $this->getDoctrine()->getManager()->getRepository("P5:Folder");
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('filename', 'text')
            ->add('folder', 'text')
            ->add('save', 'submit', array('label' => 'Upload', 'attr'=>array('class'=>'btn-primary')))
            ->setAction($this->generateUrl('upload_document'))
            ->getForm();

        $form->handleRequest($request);
        if($form->isValid()){
            $document->setUser($this->getUser());
            $document->setFolder($folderRepository->find($document->getFolder()));
            $document->setUploadDate(new \DateTime());
            $document->setLastModified(new \DateTime());

            $this->get('doctrine.orm.entity_manager')->persist($document);
            $this->get('doctrine.orm.entity_manager')->flush();
        }else {
            if($request->isMethod('POST')){
                $request->getSession()->set('upload_form', $form);
            }
        }

        return $this->redirect($this->generateUrl('documents'));
    }
}

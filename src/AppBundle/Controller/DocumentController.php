<?php

namespace AppBundle\Controller;

use P5\Model\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use P5\Model\Message;

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
        $query = $folderRepository->createQueryBuilder('f')
            ->select('f')
            ->where('f.user = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('f.root, f.lft', 'ASC');
        $folders = $query->getQuery()->getResult();
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('filename', 'text')
            ->add('folder', 'entity', array('choices' => $folders, 'class' => 'P5\Model\Folder', 'property' => 'nameHierarchy', 'placeholder' => '--Choose a folder--'))
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

            $messageCenter = $this->get('p5notification.messagecenter');
            $messageCenter->pushMessage($this->getUser(), 'A new document was uploaded by ' . $this->getUser()->getEmail(), 'document');

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
    public function sharingAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository("P5:User");
        $documentRepository = $em->getRepository("P5:Document");

        $doc = $documentRepository->find($id);

        $sharingForm = $this->createFormBuilder($doc)
            ->add('sharing_users', 'entity', array(
                'class' => 'P5:User',
                'property'     => 'username',
                'multiple'     => true,
                'attr'      => array(
                    'class'     => 'multi-select'
                ),
            ))
            ->setAction($this->generateUrl('document_sharing', array('id'=>$id)))
            ->getForm();
        $sharingForm->handleRequest($request);
        if ($sharingForm->isValid()) {
            $data = $request->request->get('form');

            foreach($data['sharing_users'] as $value) {
                $user = $userRepository->find($value);
                if (!$doc->hasSharingUsers($user)) {
                    $doc->getSharingUsers()->add($user);
                }
            }
            $em->persist($doc);
            $em->flush();

            //push notification
            $messageCenter = $this->get('p5notification.messagecenter');
            $messageCenter->pushMessage($this->getUser(), 'A document was shared to you by ' . $this->getUser()->getEmail(), 'document', $doc->getSharingUsers());

            $this->get('session')->getFlashBag()->add('success','Sharing document success!');

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

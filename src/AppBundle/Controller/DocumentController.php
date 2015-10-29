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
     * @Route("/documents/{folder_id}", name="documents", defaults={"folder_id"=null})
     * @Template()
     */
    public function indexAction(Request $request, $folder_id)
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
        $form = $this->createFormBuilder($document, array('attr'=>array('name'=>'upload_form')))
            ->add('filename', 'text', array('label'=>'Filename'))
            ->add('type', 'choice', array('choices' => $this->getParameter('document_types'), 'placeholder' => '--Choose a type--'))
            ->add('folder', 'entity', array('choices' => $folders, 'class' => 'P5\Model\Folder', 'property' => 'nameHierarchy', 'placeholder' => '--Choose a folder--'))
            ->add('save', 'submit', array('label' => 'Upload', 'attr'=>array('class'=>'mdl-button mdl-js-button mdl-button--raised mdl-button--accent')))
            ->setAction($this->generateUrl('documents', ['folder_id' => null]))
            ->getForm();
        $form->handleRequest($request);
        if($form->isValid()){
            $document->setUser($this->getUser());
            $document->setFolder($folderRepository->find($document->getFolder()));
            $document->setUploadDate(new \DateTime());
            $document->setLastModified(new \DateTime());
            $document->setDescription('Upload by ' . $this->getUser()->getEmail());

            $em->persist($document);
            $em->flush();

            $messageCenter = $this->get('p5notification.messagecenter');
            $messageCenter->pushMessage(
                $this->getUser(),
                'A new document was uploaded by ' . $this->getUser()->getEmail(),
                'document',
                array('id'=>$document->getId())
            );

            $this->addFlash(
                'success',
                'Your document was uploaded successfully!'
            );

            return $this->redirect($this->generateUrl('documents'));
        }

        if ($folder_id != null) {
            $folder = $folderRepository->find($folder_id);
            $documents = $documentRepository->getMyDocuments($this->getUser(), $folder);
        } else {
            $documents = $documentRepository->getMyDocuments($this->getUser());
        }


        $authors = $documentRepository->getAllAuthors();
        $folders = $documentRepository->getAllFolders();

        return array(
            'documents' => $documents,
            'uploadForm' => $form->createView(),
            'authors' => $authors,
            'folders' => $folders,
            'document_types' => $this->getParameter('document_types'),
        );
    }

    /**
     * @Route("/shared-documents", name="list_shared_documents")
     * @Template()
     */
    public function listSharedAction() {
        $em = $this->getDoctrine()->getManager();

        $documentRepository = $em->getRepository("P5:Document");
        $authors = $documentRepository->getAllAuthors();
        $folders = $documentRepository->getAllFolders();

        return array(
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
        $callback = $request->get('callbackRoute');
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

            if($callback){
                return $this->redirectToRoute($callback, array('id'=>$id));
            }
            else{
                return $this->redirect($this->generateUrl('documents'));
            }
        }

        return array(
            'sharingForm' => $sharingForm->createView(),
            'callbackRoute' => $callback,
        );
    }

    /**
     * @var int $id
     * @Route("/remove_document/{id}", name="remove_document")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $documentRepository = $em->getRepository('P5:Document');
        $document = $documentRepository->find($id);
        $em->remove($document);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success','The document was removed sucessfully!');
        return $this->redirectToRoute('documents');
    }

    /**
     * @var int $id
     * @Route("/document/{id}", name="document_details")
     * @Template()
     * @return array
     */
    public function showAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $documentRepository = $em->getRepository('P5:Document');
        $document = $documentRepository->find($id);
        $folder = $document->getFolder();
        $folderTree = array();
        $level = $folder->getLvl();
        for($i=0; $i<=$level; $i++){
            $folderTree[$i] = $folder->getName();
            $folder = $folder->getParent();
        }

        return array(
            'document' => $document,
            'user' => $this->get('security.token_storage')->getToken()->getUser(),
            'folderTree' => array_reverse($folderTree),
        );
    }

    /**
     * @var int $id
     * @Route("/document/edit/{id}", name="edit_document")
     * @Template()
     * @return array
     */
    public function editAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $documentRepository = $em->getRepository('P5:Document');
        $document = $documentRepository->find($id);

        $form = $this->createFormBuilder($document, array('attr'=>array('name'=>'edit_form')))
            ->add('filename', 'text', array('label'=>'Filename'))
            ->add('description', 'textarea', array('label'=>'Description', 'attr'=>array('rows'=>'12')))
            ->setAction($this->generateUrl('documents'))
            ->getForm();
        $form->handleRequest($request);
        if($form->isValid()){
            $em->persist($document);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','The details of the document was updated successfully!');

            return $this->redirectToRoute('document_details', array('id'=>$id));
        }

        return array(
            'document' => $document,
            'editForm' => $form->createView(),
        );
    }
}

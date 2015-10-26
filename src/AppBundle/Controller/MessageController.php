<?php

namespace AppBundle\Controller;

use P5\Model\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use P5\Model\Message;

class MessageController extends Controller
{
    /**
     * @var int $id
     * @return mixed
     * @Route("/message/{id}", name="view_message")
     */
    public function viewAction($id, Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $messageRepository = $em->getRepository('P5:Message');
        $message = $messageRepository->find($id);
        $muRepository = $em->getRepository('P5:MessageUser');
        $msgUser = $muRepository->findOneBy(array('message'=>$message, 'toUser'=>$this->getUser()));
        $msgUser->setStatus(true);
        $em->persist($msgUser);
        $em->flush();
        switch($message->getType()){
            case 'document':
                if($message->getParameters()){
                    return $this->redirectToRoute('document_details', (array)$message->getParameters());
                }
                else{
                    return $this->redirectToRoute('documents');
                }
                break;
            default:
                break;
        }
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/8/15
 * Time: 3:28 PM
 */

namespace P5NotificationBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use P5\Model\Message;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MessageCenter
{
    private $em;
    private $token;
    public function __construct(EntityManager $em, TokenStorage $token){
        $this->em = $em;
        $this->token = $token;
    }

    public function pushMessage(Message $message){
        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function getNotificationNumber(){
        $user = $this->token->getToken()->getUser();
        return count($user->getReceivedMessages());
    }

    public function getNotifications(){
        $user = $this->token->getToken()->getUser();
        return $user->getReceivedMessages();
    }
}
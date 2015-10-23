<?php

namespace V3d\Bundle\ApplicationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

use P5\Model\Document;
use P5\Model\Folder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;



/**
 * Fixture exécutée lors d'un : php app/console doctrine:fixtures:load
 */
class LoadBasicData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnityMAnager
     */
    private $em;

    /**
     * @var EntityUtils service reference
     */
    private $entityUtils;

    /**
     * @var Fairutils service reference
     */
    private $fairUtils;

    /**
     * @var Settings manager service reference
     */
    private $settingsManager;

    /**
     * @var FOS userManager reference
     */
    private $userManager;

    /**
     * @var FOS userManipulator reference
     */
    private $userManipulator;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $this->em = $em;
        $this->userManager     = $this->container->get('fos_user.user_manager');
        $this->userManipulator = $this->container->get('fos_user.util.user_manipulator');
        $datasetPath = realpath(dirname(__FILE__));
        $dataset     = Yaml::parse(file_get_contents($datasetPath. '/dataset.yml'));

        $usersObject = $dataset["users"];
        $this->injectUsers($usersObject);
        $this->injectFolders($dataset['folders']);
        $this->injectDocuments($dataset['documents']);
        //$this->em->flush();
    }

    private function injectUsers($data)
    {
        //Create & Link Users
        foreach ($data as $userName => $userData) {
            $user = $this->userManager->findUserByUsername($userName);

            $password = isset($userData["password"])?$userData["password"]:"password";
            $enabled  = isset($userData["enabled"])?$userData["enabled"]:true;
            $email    = isset($userData["email"])?$userData["email"]:"anemail@here.loc";

            if (!$user) {
                $user = $this->userManipulator->create($userName, $password, $email, $enabled, false);
            } else {
                $user->setEnabled($enabled);
                $user->setEmail($email);
                $user->setPlainPassword($password);
            }

            if (isset($userData["roles"])) {
                $user->setRoles($userData["roles"]);
            }

            $this->userManager->updateUser($user);
            $this->em->persist($user);
        }
        $this->em->flush();
    }
    private  function injectFolders($data, $parent = null) {
        $folderRepository = $this->em->getRepository('P5:Folder');
        foreach($data as $folderName => $folderData) {
            $folder = $folderRepository->findOneByName($folderName);
            if(!$folder) {
                $folder = new Folder();
            }

            $folder->setName($folderName);
            $folder->setUser($this->userManager->findUserByUsername($folderData['creator']));
            $folder->setUploadDate(new \DateTime());
            $folder->setLastModified(new \DateTime());
            if($parent != null) {
                $folder->setParent($parent);
            }

            //$folderRepository->persistAsFirstChild($folder);
            $this->em->persist($folder);

            //find all childrent;
            if($this->arrayHasValue($folderData, 'children')) {
                $this->injectFolders($folderData['children'], $folder);
            }
        }
        $this->em->flush();
    }

    private function injectDocuments($data){
        $documentRepository = $this->em->getRepository('P5:Document');
        $folderRepository = $this->em->getRepository('P5:Folder');
        $messageCenter = $this->container->get('p5notification.messagecenter');

        foreach($data as $documentName=>$documentData){
            $document = $documentRepository->findOneByFilename($documentName);
            if(!$document){
                $document = new Document();
            }
            $document->setFilename($documentName);
            $creator = $this->userManager->findUserByUsername($documentData['creator']);
            $folder = $folderRepository->findOneByName($documentData['folder']);
            $document->setFolder($folder);
            $document->setUploadDate(new \DateTime(isset($documentData["upload_date"])?$documentData["upload_date"]:"2014-01-01 10:00:00"));
            $document->setLastModified(new \DateTime(isset($documentData["last_modified"])?$documentData["last_modified"]:"2014-01-01 10:00:00"));
            $document->setUser($creator);
            $document->setType($documentData['type']);
            $document->setDescription('Uploaded by ' . $creator->getEmail());
            if($this->arrayHasValue($documentData, 'shareToUsers')) {
                $shareUsers = new ArrayCollection();
                //push notification
                foreach($documentData['shareToUsers'] as $shareUsername) {
                    $shareUsers->add($this->userManager->findUserByUsername($shareUsername));
                }
                $messageCenter->pushMessage($creator, 'A document was shared to you by ' . $creator->getEmail(), 'document', $shareUsers->getValues());
                $document->setSharingUsers($shareUsers);
            }
            $this->em->persist($document);
        }
        $this->em->flush();
    }

    private function arrayHasValue($array, $key){
        $result = true;
        if(!isset($array)) {
            $result = false;
        } else if (!array_key_exists($key, $array)) {
            $result = false;
        } else if(!isset($array[$key])) {
            $result = false;
        }

        return $result;
    }
}

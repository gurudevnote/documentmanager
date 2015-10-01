<?php

namespace V3d\Bundle\ApplicationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

use V3d\Model\Account;
use V3d\Model\Fair;
use V3d\Model\Booth;
use V3d\Model\FairEvent;
use V3d\Model\Media;
use V3d\Bundle\ApplicationBundle\Utility\Flag\V3dItemBindingRoleFlag;

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
        $this->em->flush();
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

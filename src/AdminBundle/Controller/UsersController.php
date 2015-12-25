<?php

namespace AdminBundle\Controller;

use P5\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\UserType;

class UsersController extends Controller
{
    /**
     * @Route("/user", name="admin_user_list")
     * @Template()
     */
    public function listAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return array('users' => $users);
    }

    public function createAction(Request $request)
    {
    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param User $user The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $user)
    {
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     */
    public function newAction()
    {
        $user = new Event();
        $form = $this->createCreateForm($user);

        return $this->render('P5:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('P5:User')->findOneBy(array('slug' => $slug));

        if (!$user) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($user->getId());

        return $this->render('P5:User:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Creates a form to edit a Event entity.
     *
     * @param User $user The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $user)
    {
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('admin_user_edit', array('id' => $user->getId())),
            'method' => 'PUT',
            'validation_groups' => array('admin_edit_user'),
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => ['class' => 'btn btn-primary']));

        return $form;
    }
    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/user/{id}/edit", name="admin_user_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('P5:User')->find($id);

//        $formFactory = $this->get('fos_user.registration.form.factory');
//        $form = $formFactory->createForm(array('validation_groups'=> array('edit_user')));
//        $form->add('submit', 'submit', array('label' => 'Sign Up', 'attr' => ['class' => 'btn btn-info']));
//        $form->remove('plainPassword');
//        $form->setData($user);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Update user success');

            return $this->redirect($this->generateUrl('admin_user_list'));
        }

        return array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/delete", name="admin_user_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('P5:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * @param string $id
     * @param string $format
     *
     * @return url
     * @Route("/{id}/enable.{format}", name="admin_user_set_enable")
     */
    public function changeEnable($id, $format = 'html')
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        if ($user->isEnabled()) {
            $value = false;
        } else {
            $value = true;
        }
        $user->setEnabled($value);
        $userManager->updateUser($user);
//        die(var_dump($user->toArray()));

        if ($format == 'json') {
            $data = array(
                'enable' => $user->isEnabled(),
            );

            $response = new JsonResponse($data);

            return $response;
        }
        $url = $this->generateUrl('admin_user_list');

        return $this->redirect($url);
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete this user', 'attr' => ['class' => 'btn btn-outline btn-link']))
            ->getForm()
            ;
    }
}

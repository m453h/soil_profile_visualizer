<?php

namespace AppBundle\Controller\UserAccounts;

use AppBundle\Entity\Configuration\Course;
use AppBundle\Entity\UserAccounts\Staff;
use AppBundle\Entity\UserAccounts\User;
use AppBundle\Form\Accounts\ResetPasswordForm;
use AppBundle\Form\Accounts\StaffRoleFormType;
use AppBundle\Form\Accounts\UserFormType;
use Exception;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AccountController extends Controller
{


    /**
     * @Route("/change-my-password", name="change_account_password")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordForm::class,$user);

        // only handles data on POST
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();

            $password = $data->getPassword();

            $currentPasswordHash = $em->getRepository('AppBundle:UserAccounts\User')
                ->findCurrentUserHash($user->getId());

            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);

            if ($encoder->isPasswordValid($currentPasswordHash, $password, $user->getSalt()))
            {

                $this->addFlash('error', 'You can not use your current password as the new password');

                return $this->redirectToRoute('change_account_password');
            }

            $encoder = $this->get('security.password_encoder');
            $data->setPassword($encoder->encodePassword($user,$data->getPassword()));
            $data->setLastPasswordUpdateDate(new \DateTime());



            $em->persist($data);
            $em->flush();

            $this->addFlash('success', 'Password successfully changed');


            return $this->redirectToRoute('app_home_page');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'user.accounts/reset.password',
                'form'=>$form->createView(),
                'title'=>'Password change form'
            )

        );
    }
    
}
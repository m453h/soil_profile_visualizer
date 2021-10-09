<?php

namespace AppBundle\Controller\SpatialAPI;

use AppBundle\Entity\UserAccounts\User;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller{

    /**
     * @Route("/api/verifyToken", name="api_verify_token")
     * @param Request $request
     * @return Response
     *
     */
    public function verifyTokenAction(Request $request)
    {
        $content =  $request->getContent();

        $data = json_decode($content,true);

        $em = $this->getDoctrine()->getManager();

        $data['status'] = 'FAIL';

        $arrayParser = $this->get('app.helper.array_parser');

        $token = $arrayParser->getFieldValue($data,'authToken');

        if($token!=null)
        {
            $user = $em->getRepository('AppBundle:UserAccounts\User')
                ->findOneBy(['token' => $token]);
        }
        else
        {
            $user = null;
        }

        if($user instanceof User)
        {
            $data['status'] = 'PASS';
        }

        unset($data['token']);

        return new JsonResponse($data);
    }


    /**
     * @Route("/api/login", name="api_login_app_user")
     * @param Request $request
     * @return Response
     */
    public function loginAuthorizationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parser = $this->get('app.helper.array_parser');

        $content = $request->getContent();
        //$content ='{"username":"patient01","password":"password"}';
        $data = json_decode($content, true);

        $username = $parser->getFieldValue($data,'username');
        $password = $parser->getFieldValue($data,'password');

        $user = $em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['username' => $username]);

        if($user instanceof User) {
            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);

            if ($user->getAccountStatus() == 'A')
            {

                if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt()))
                {
                   $data['status']='PASS';

                    $now = new \DateTimeImmutable();
                    $token = base64_encode(random_bytes(64));

                    $user->setToken($token);
                    $user->setLastTokenRequest($now);
                    $em->persist($user);
                    $em->flush();

                    $data['AuthToken'] = $token;
                    $data['fullname'] = $user->getFullName();
                    $data['email'] = $user->getEmail();
                    $data['mobile'] = $user->getMobilePhone();
                }
                else
                {
                    $data['status']='FAIL_INCORRECT_PASSWORD';
                    $data['message']='Incorrect username or password';
                }
            }
            else
            {
                $data['status'] ='FAIL_INACTIVE';
                $data['message']='User account is not active';

            }
        }
        else
        {
            $data['status']='FAIL_INCORRECT_PASSWORD';
            $data['message']='Incorrect username or password';
        }

        return new JsonResponse($data);
    }


    /**
     * @Route("/api/registration", name="api_register_app_user")
     * @param Request $request
     * @return Response
     */
    public function userRegistrationAction(Request $request){
	
        $em = $this->getDoctrine()->getManager();

        $parser = $this->get('app.helper.array_parser');

        $content = $request->getContent();

        $data = json_decode($content, true);

        $fullName = $parser->getFieldValue($data,'fullName');
        $phoneNumber = $parser->getFieldValue($data,'phoneNumber');
        $email = $parser->getFieldValue($data,'email');
        $password = $parser->getFieldValue($data,'password');
        $password = $this->get('security.password_encoder')->encodePassword(new User(),$password);
        $token = base64_encode(random_bytes(64));

        $user = new User();
        $user->setFullName($fullName);
        $user->setMobilePhone($phoneNumber);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setUsername($phoneNumber);
        $user->setAccountStatus("A");
        $user->setIsCustomer(true);
        $user->setDateCreated(new \DateTimeImmutable());
        $user->setToken($token);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $data = [];
        $data['AuthToken'] = $token;
        $data['fullName'] = $fullName;
        $data['email'] = $email;
        $data['status'] = 'PASS';
        return new JsonResponse($data);
    }

}

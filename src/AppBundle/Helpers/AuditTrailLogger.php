<?php

namespace AppBundle\Helpers;

use AppBundle\Entity\UserAccounts\AuditTrail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AuditTrailLogger
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $storageInterface;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * AuditTrailLogger constructor.
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     * @param TokenStorageInterface $storageInterface
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack,TokenStorageInterface $storageInterface)
    {

        $this->entityManager = $entityManager;
        $this->storageInterface = $storageInterface;
        $this->requestStack = $requestStack;
    }


    public function logUserAction($description,$action,$originalData,$finalData)
    {

        $user = $this->storageInterface->getToken()->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $encoders  = array(new XmlEncoder(),new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers,$encoders);



        $log = new AuditTrail();
        $log->setEntityDescriptor($description);
        $log->setAction($action);
        $log->setTimeLogged(new \DateTimeImmutable());
        $log->setUser($user);
        $log->setIpAddress($request->getClientIp());
        $log->setUserAgent($request->headers->get('User-Agent'));

        if($originalData!=null)
        {
            $log->setOriginalData($serializer->serialize($originalData,'json'));
        }

        if($finalData!=null)
        {
            $log->setFinalData($serializer->serialize($finalData, 'json'));
        }

        try
        {
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        }
        catch (ORMException $e)
        {

        }
    }



}
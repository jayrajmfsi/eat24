<?php
/**
 *  BaseService for providing commonly used Symfony Services to other Custom Services of Application.
 *  This Service class should be extended as parent Service to the custom Application Service.
 *
 *  @category Service
 */

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class BaseService
{
    /**
     * @var ContainerInterface
     */
    protected $serviceContainer;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @return ContainerInterface
     */
    public function getServiceContainer(): ContainerInterface
    {
        return $this->serviceContainer;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @param ContainerInterface $serviceContainer
     */
    public function setServiceContainer(ContainerInterface $serviceContainer): void
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     *  Function to generate a new (Most Probably Unique) Transaction Id
     *  for creating slug fields.
     *
     *  @param boolean $createHash(default = true)
     *
     *  @return string
     */
    public function generateNewTransactionId($createHash = true)
    {
        $currentTime = explode(" ", microtime());
        $transactionId = substr($currentTime[0], 2, 8) . $currentTime[1];
        $transactionId = $transactionId . rand(0, 100) . rand(20, 787);
        return (true === $createHash) ? sha1($transactionId) : (string)$transactionId;
    }

    /**
     *  Function to get Current User From Token Storage.
     *
     *  @return User
     */
    public function getCurrentUser()
    {
        // Fetching User Object from Token Storage.
        return $this->serviceContainer->get('security.token_storage')->getToken()->getUser();
    }
}
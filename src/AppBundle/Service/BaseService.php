<?php
/**
 *  BaseService for providing commonly used Symfony Services to other Custom Services of Application.
 *  This Service class should be extended as parent Service to the custom Application Service.
 *
 *  @category Service
 *  @author <jayraja@mindfiresolutions.com>
 */

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BaseService
 * @package AppBundle\Service
 */
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
     * Get service container
     * @return ContainerInterface
     */
    public function getServiceContainer(): ContainerInterface
    {
        return $this->serviceContainer;
    }

    /**
     * Get entity manager
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * get logger
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * get translator
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * set service container
     * @param ContainerInterface $serviceContainer
     */
    public function setServiceContainer(ContainerInterface $serviceContainer): void
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * set entity manager
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * set logger
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * set translator
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
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

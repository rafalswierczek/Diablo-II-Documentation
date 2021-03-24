<?php declare(strict_types=1);

namespace App\Service\Utils;

use Symfony\Component\HttpFoundation\{Request, RequestStack};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Security;
use Psr\Log\LoggerInterface;
use App\Entity\Application\User;
use App\Service\Utils\NotificationHandler;

class Container
{
    private RequestStack $requestStack;
    private ParameterBagInterface $parameterBag;
    private ContainerInterface $container;
    private ValidatorInterface $validator;
    private Security $security;
    private LoggerInterface $logger;
    private NotificationHandler $notificationHandler;

    public function __construct(
        RequestStack $requestStack,
        ParameterBagInterface $parameterBag,
        ContainerInterface $container,
        ValidatorInterface $validator,
        Security $security,
        LoggerInterface $logger,
        NotificationHandler $notificationHandler
    )
    {
        $this->requestStack = $requestStack;
        $this->parameterBagInterface = $parameterBag;
        $this->containerInterface = $container;
        $this->validatorInterface = $validator;
        $this->security = $security;
        $this->loggerInterface = $logger;
        $this->notificationHandler = $notificationHandler;
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    public function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    public function getUser(): ?User
    {
        return $this->security->getUser();
    }

    public function getUserId(): ?int
    {
        return ($user = $this->security->getUser()) ? $user->getId() : null;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getNotificationHandler(): NotificationHandler
    {
        return $this->notificationHandler;
    }
}
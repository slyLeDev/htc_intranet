<?php
namespace App\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use App\Entity\User;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Listener that updates the last activity of the authenticated user
 */
class UserActivityListener
{
    protected EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * Update the user "lastActivity" on each request
     * @param ControllerEvent $event
     */
    public function onCoreController(ControllerEvent $event)
    {
        // Check that the current request is a "MAIN_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        // Check token authentication availability
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if (($user instanceof User) && !($user->isActiveNow()) ) {
                $user->setLastActivityAt(new \DateTimeImmutable());
                $this->entityManager->flush($user);
            }
        }
    }
}
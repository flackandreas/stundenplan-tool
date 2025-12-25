<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SchoolFilterConfigurator
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $school = $user->getSchool();
        if (!$school) {
            return;
        }

        $filter = $this->em->getFilters()->enable('school_filter');
        $filter->setParameter('school_id', $school->getId());
    }
}

<?php
/**
 * @author hR.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class DashboardBuilder
{
    private CustomerRepository $customerRepository;
    private DealRepository $dealRepository;
    private UserRepository $userRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        DealRepository $dealRepository,
        UserRepository $userRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->dealRepository = $dealRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        //$userConsultant = $this->userRepository->findByRole(User::ROLE_CONSULTANT, true, false, true);
        $userConsultant = $this->userRepository->findBy([], ['lastActivityAt' => 'DESC']);

        return [
            'customer_count' => count($this->customerRepository->findAll()),
            'deal_count' => count($this->dealRepository->findAll()),
            'user_consultant_count' => count($userConsultant),
            'user_consultant' => $userConsultant,
        ];
    }
}
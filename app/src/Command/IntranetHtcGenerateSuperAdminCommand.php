<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class IntranetHtcGenerateSuperAdminCommand extends Command
{
    protected static $defaultName = 'intranet_htc:generate-super-admin';
    protected static $defaultDescription = 'Generate superadmin user';
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    private UserRepository $userRepository;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ParameterBagInterface $parameterBag,
        string $name = null
    ) {
        parent::__construct($name);
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        /*$this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->addSuUser(
            $this->parameterBag->get('user_superadmin_fullname'),
            $this->parameterBag->get('user_superadmin_email'),
            $this->parameterBag->get('user_superadmin_pwd')
        );

        $this->addSuUser(
            'Marylène Ratsisalozafy',
            'marylene@human-talent-consulting.com',
            $this->parameterBag->get('user_superadmin_pwd')
        );

        $io->success('All SU registred with success !');

        return Command::SUCCESS;
    }

    private function addSuUser(string $fullName, string $email, string $password)
    {
        $superAdminUser = $this->userRepository->findOneBy(['email' => $email]) ?? new User();
        $superAdminUser->setEmail($email);
        $superAdminUser->setFullName($fullName);
        $superAdminUser->setRoles([User::ROLE_SUPER_ADMIN]);
        $superAdminUser->setIsEnable(true);
        // encode the plain password
        $superAdminUser->setPassword(
            $this->userPasswordHasher->hashPassword(
                $superAdminUser,
                $password
            )
        );

        if (!$superAdminUser->getId()) {
            $this->entityManager->persist($superAdminUser);
        }

        $this->entityManager->flush();
    }
}

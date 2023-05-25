<?php

namespace App\DataFixtures;

use App\Entity\BatchCustomer;
use App\Entity\Customer;
use App\Entity\Deal;
use App\Repository\CustomerRepository;
use App\Repository\JobSectorRepository;
use App\Repository\UserRepository;
use App\Service\DealReferenceBuilder;
use App\Service\ResetAutoincrementEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CustomerFixtures extends Fixture implements OrderedFixtureInterface
{
    private JobSectorRepository $jobSectorRepository;
    private ParameterBagInterface $parameterBag;
    private ResetAutoincrementEntity $resetAutoincrementEntity;
    private UserRepository $userRepository;
    private DealReferenceBuilder $dealReferenceBuilder;

    public function __construct(
        JobSectorRepository $jobSectorRepository,
        UserRepository $userRepository,
        ParameterBagInterface $parameterBag,
        ResetAutoincrementEntity $resetAutoincrementEntity,
        DealReferenceBuilder $dealReferenceBuilder
    ) {
        $this->jobSectorRepository = $jobSectorRepository;
        $this->userRepository = $userRepository;
        $this->parameterBag = $parameterBag;
        $this->resetAutoincrementEntity = $resetAutoincrementEntity;
        $this->dealReferenceBuilder = $dealReferenceBuilder;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->resetAutoincrementEntity->resetAutoincrement('customer');
        $this->resetAutoincrementEntity->resetAutoincrement('deal');

        $faker = Factory::create('fr_FR');
        $customers = [];
        for ($i = 0; $i < 120; $i++) {
            $customers[$i] = new Customer();
            $customers[$i]->setName($faker->company);
            $customers[$i]->setManager($faker->name);
            $customers[$i]->setInterlocutor($faker->name);
            $customers[$i]->setPhone($faker->e164PhoneNumber);
            $customers[$i]->setWebsite($faker->domainName);
            $customers[$i]->setLocality($faker->address);

            //job sector
            for ($j = 0; $j < rand(1, 6); $j++) {
                $fakeJobSector = $this->jobSectorRepository->find(rand(0, 1000));
                if ($fakeJobSector) {
                    $customers[$i]->addSector($fakeJobSector);
                }
            }

            $manager->persist($customers[$i]);
            $manager->flush($customers[$i]);

            //create deals
            $deals = [];
            for ($k = 0; $k < rand(1, 10); $k++) {
                $deals[$k] = new Deal();
                $deals[$k]->setReference($this->dealReferenceBuilder->generate($faker->unixTime));
                $deals[$k]->setJobName($faker->jobTitle);
                $deals[$k]->setJobDescription($faker->realText(1500));
                $deals[$k]->setQuantity(rand(1, 5));
                $theStatus = rand(0, 2);
                $deals[$k]->setDeadline($faker->dateTimeThisYear);
                if (Deal::STATUS_PENDING === $theStatus) {
                    $deals[$k]->setDeadline($faker->dateTimeInInterval('-1 week', '+4 week'));
                }

                $deals[$k]->setStatus(rand(0, 2));
                $deals[$k]->setComment($faker->text);
                $salaryState = rand(0, 1);
                $deals[$k]->setSalaryState($salaryState);
                if (Deal::SALARY_VARIABLE === $salaryState) {
                    $deals[$k]->setSalaryMin(rand(5, 7) * 100000);
                    $deals[$k]->setSalaryMax(rand(1, 9) * 1000000);
                }

                if (Deal::SALARY_EXACT === $salaryState) {
                    $deals[$k]->setSalaryExact(rand(1, 9) * 1000000);
                }

                // customer
                $deals[$k]->setCustomer($customers[$i]);
                $customers[$i]->addDeal($deals[$k]);

                $deals[$k]->setDealFilename($faker->file(
                    $this->parameterBag->get('fake_file_directory'),
                    $this->parameterBag->get('deal_file_directory'),
                    false
                ));

                // consultant
                for ($l = 0; $l < rand(1, 2); $l++) {
                    $consultant = $this->userRepository->find(rand(1, 15));
                    if ($consultant) {
                        $deals[$k]->addResponsibleConsultant($consultant);
                    }
                }

                $manager->persist($deals[$k]);
            }

            $manager->flush();
        }
    }

    public function getOrder(): int
    {
        return 100; // smaller means sooner
    }
}

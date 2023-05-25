<?php
/**
 * @author hR.
 */

namespace App\Command;

use App\DataInjector\InterviewedProfileInjector;
use App\DataInjector\ReceivedProfileInjector;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** IntranetHtcDataInjectorCommand */
class IntranetHtcDataInjectorCommand extends Command
{
    protected static $defaultName = 'intranet_htc:data_injector';
    protected static $defaultDescription = 'Data Injector';
    /**
     * @var ReceivedProfileInjector
     */
    private $receivedProfileInjector;
    /**
     * @var InterviewedProfileInjector
     */
    private $interviewedProfileInjector;

    public function __construct(
        ReceivedProfileInjector $receivedProfileInjector,
        InterviewedProfileInjector $interviewedProfileInjector,
        string $name = null
    ) {
        parent::__construct($name);
        $this->receivedProfileInjector = $receivedProfileInjector;
        $this->interviewedProfileInjector = $interviewedProfileInjector;
    }

    protected function configure(): void
    {
        $this
            ->addOption('received_profile', null, InputOption::VALUE_NONE, 'Inject received profile')
            ->addOption('interviewed_profile', null, InputOption::VALUE_NONE, 'Inject interviewed profile')
            ->addOption('simulate', null, InputOption::VALUE_NONE, 'Simulate')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $reveivedProfile = $input->getOption('received_profile');
        $interviewedProfile = $input->getOption('interviewed_profile');
        $simulate = $input->getOption('simulate');

        if ($interviewedProfile) {
            $this->interviewedProfileInjector->inject($io, $simulate);

            return Command::SUCCESS;
        }

        if ($reveivedProfile) {
            $this->receivedProfileInjector->inject($io, $simulate);

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}

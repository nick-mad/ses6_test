<?php

declare(strict_types=1);

namespace App\Presentation\Console\Subscription;

use App\Application\Subscription\Service\ScannerServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class ScanCommand extends Command
{
    private ScannerServiceInterface $scannerService;

    public function __construct(ScannerServiceInterface $scannerService)
    {
        parent::__construct();
        $this->scannerService = $scannerService;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('subscription:scan');
        $this->setDescription('Scan for new GitHub releases and notify subscribers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Scanning for new releases...</info>');

        try {
            $this->scannerService->scan();
            $output->writeln('<info>Scan completed successfully.</info>');
            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $output->writeln(sprintf('<error>Scan failed: %s</error>', $exception->getMessage()));
            return Command::FAILURE;
        }
    }
}

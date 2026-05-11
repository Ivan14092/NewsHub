<?php

namespace App\Command;

use App\Message\UpdatePricesMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:update-prices',
    description: 'Dispatch price update messages to the queue',
)]
final class UpdatePricesCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'symbol',
            InputArgument::OPTIONAL,
            'Symbol to update (BTC or USD), leave empty for both',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symbol = $input->getArgument('symbol');

        if ($symbol) {
            $this->messageBus->dispatch(new UpdatePricesMessage(strtoupper($symbol)));
            $output->writeln("<info>Dispatched update for {$symbol}</info>");
        } else {
            $this->messageBus->dispatch(new UpdatePricesMessage('BTC'));
            $this->messageBus->dispatch(new UpdatePricesMessage('USD'));
            $output->writeln('<info>Dispatched update for BTC and USD</info>');
        }

        return Command::SUCCESS;
    }
}
<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Console\Command;

use ParagonIE\ConstantTime\Hex;
use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:payment:generate-key',
    description: 'Generate encryption key for Sylius payment encryption.',
)]
final class GenerateEncryptionKeyCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating encryption key for Sylius payment encryption');

        try {
            $output->writeln('Key: ' . KeyFactory::export(KeyFactory::generateEncryptionKey())->getString());
        } catch (\TypeError) {
            $output->writeln('Key could not be generated. Please, make sure that PHP supports libsodium');

            return Command::FAILURE;
        }

        $output->writeln('Remember to update your configuration with this key');

        return Command::SUCCESS;
    }
}

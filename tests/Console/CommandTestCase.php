<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tester\TestCase;

abstract class CommandTestCase extends TestCase
{
	abstract protected function createCommand(): Command;

	protected function runCommand(
		InputInterface $input,
		OutputInterface $output,
	): int
	{
		$command = $this->createCommand();

		$application = new Application();
		$application->setAutoExit(false);
		$application->add($command);

		return $application->run($input, $output);
	}
}

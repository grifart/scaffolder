<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Console;

use Grifart\ClassScaffolder\Console\CheckCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class CheckCommandTest extends CommandTestCase
{
	public function testCheck(): void
	{
		$input = new ArrayInput(['check', 'definition' => __DIR__ . '/check']);
		$output = new BufferedOutput();

		$exitCode = $this->runCommand($input, $output);

		Assert::same(1, $exitCode);
		Assert::matchFile(__DIR__ . '/CheckCommandTest.expected.txt', $output->fetch());
	}

	protected function createCommand(): Command
	{
		return new CheckCommand();
	}
}

(new CheckCommandTest())->run();

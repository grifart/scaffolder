<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Console;

use Grifart\ClassScaffolder\Console\ScaffoldCommand;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class ScaffoldCommandTest extends CommandTestCase
{
	public function testSuccess(): void
	{
		$input = new ArrayInput(['scaffold', 'definition' => __DIR__ . '/definitions/good.definition.php']);
		$output = new BufferedOutput();

		$exitCode = $this->runCommand($input, $output);

		Assert::same(0, $exitCode);
		Assert::matchFile(__DIR__ . '/ScaffoldCommandTest.expected.success.txt', $output->fetch());
	}

	public function testVerbose(): void
	{
		$input = new ArrayInput(['scaffold', 'definition' => __DIR__ . '/definitions/good.definition.php']);
		$output = new BufferedOutput(BufferedOutput::VERBOSITY_VERBOSE);

		$exitCode = $this->runCommand($input, $output);

		Assert::same(0, $exitCode);
		Assert::matchFile(__DIR__ . '/ScaffoldCommandTest.expected.verbose.txt', $output->fetch());
	}

	public function testVeryVerbose(): void
	{
		$input = new ArrayInput(['scaffold', 'definition' => __DIR__ . '/definitions/good.definition.php']);
		$output = new BufferedOutput(BufferedOutput::VERBOSITY_VERY_VERBOSE);

		$exitCode = $this->runCommand($input, $output);

		Assert::same(0, $exitCode);
		Assert::matchFile(__DIR__ . '/ScaffoldCommandTest.expected.veryVerbose.txt', $output->fetch());
	}

	public function testFailure(): void
	{
		$input = new ArrayInput(['scaffold', 'definition' => __DIR__ . '/definitions/failing.definition.php']);
		$output = new BufferedOutput();

		$exitCode = $this->runCommand($input, $output);

		Assert::same(1, $exitCode);
		Assert::matchFile(__DIR__ . '/ScaffoldCommandTest.expected.failing.txt', $output->fetch());
	}

	public function testInvalid(): void
	{
		$input = new ArrayInput(['scaffold', 'definition' => __DIR__ . '/definitions/invalid.definition.php']);
		$output = new BufferedOutput();

		$exitCode = $this->runCommand($input, $output);

		Assert::same(1, $exitCode);
		Assert::matchFile(__DIR__ . '/ScaffoldCommandTest.expected.invalid.txt', $output->fetch());
	}

	protected function createCommand(): Command
	{
		return new ScaffoldCommand();
	}
}

(new ScaffoldCommandTest())->run();

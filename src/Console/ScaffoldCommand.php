<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Console;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Grifart\ClassScaffolder\FileResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'scaffold',
	description: 'Generate classes from given definitions.',
	aliases: ['grifart:scaffolder:scaffold', 'grifart:scaffolder:generateClass'],
)]
final class ScaffoldCommand extends ScaffolderCommand
{
	protected function configure(): void
	{
		parent::configure();
		$this->addOption('no-readonly', NULL, InputOption::VALUE_NONE, 'Generated files are marked as read only by default (using chmod). Use this option to turn off this behaviour.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		try {
			$definitionFiles = $this->locateDefinitionFiles($input);
		} catch (\Throwable $error) {
			$this->printError($error, $output);
			return 1;
		}

		/** @var FileResult[] */
		$results = [];
		$isSuccess = true;

		$processedFiles = 0;
		$total = count($definitionFiles);

		$output->writeln(\sprintf('Processing %d definition file%s:%s', $total, $total !== 1 ? 's' : '', \PHP_EOL));

		foreach ($definitionFiles as $definitionFile) {
			$results[] = $result = $this->processFile($definitionFile, $input);

			if ($result->isSuccessful()) {
				$output->write('.');
			} else {
				$output->write('<error>F</error>');
				$isSuccess = false;
			}

			if (++$processedFiles % 40 === 0) {
				$output->writeln('');
			}
		}

		$output->writeln(\PHP_EOL);

		$this->printResults($results, $output);

		return (int) ! $isSuccess;
	}

	protected function processDefinition(
		ClassDefinition $definition,
		DefinitionFile $definitionFile,
		InputInterface $input,
	): DefinitionResult
	{
		try {
			$generatedFile = $this->classGenerator->generateClass($definition);
		} catch (\Throwable $error) {
			return DefinitionResult::error($definition, $error);
		}

		$code = (string) $generatedFile;
		$targetPath = $definitionFile->resolveTargetFileFor($definition);

		if (\file_exists($targetPath)) {
			\chmod($targetPath, 0664); // some users accessing files using group permissions
		}

		if (\file_put_contents($targetPath, $code) === false) {
			return DefinitionResult::error($definition, new \RuntimeException('Failed to write file.'));
		}

		if ( ! $input->getOption('no-readonly')) {
			\chmod($targetPath, 0444); // read-only -- assumes single user system
		}

		return DefinitionResult::success($definition);
	}
}

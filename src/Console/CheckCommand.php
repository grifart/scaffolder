<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Console;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Grifart\ClassScaffolder\FileResult;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckCommand extends ScaffolderCommand
{
	protected function configure(): void
	{
		parent::configure();

		$this->setName('check')
			->setDescription('Checks that all generated classes match given definitions.')
			->setAliases(['grifart:scaffolder:check']);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		try {
			$definitionFiles = $this->locateDefinitionFiles($input);
		} catch (\Throwable $error) {
			$this->printError($error, $output);
			return 1;
		}

		$results = [];
		$isSuccess = true;

		$processedFiles = 0;
		$total = \count($definitionFiles);

		$output->writeln(\sprintf('Checking %d definition file%s:%s', $total, $total !== 1 ? 's' : '', \PHP_EOL));

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

		if ( ! \file_exists($targetPath)) {
			return DefinitionResult::success($definition);
		}

		$contents = \file_get_contents($targetPath);
		if ($contents === false) {
			return DefinitionResult::error($definition, new \RuntimeException('Failed to read file.'));
		}

		if ($contents !== $code) {
			return DefinitionResult::error($definition, new \RuntimeException('The generated file contains changes that will be lost if you generate it again.'));
		}

		return DefinitionResult::success($definition);
	}
}

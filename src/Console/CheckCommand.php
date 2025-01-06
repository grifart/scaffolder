<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Console;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'check',
	description: 'Checks that all generated classes match given definitions.',
	aliases: ['grifart:scaffolder:check'],
)]
final class CheckCommand extends ScaffolderCommand
{
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
			return DefinitionResult::error($definition, new \RuntimeException('There is no generated file for given definition.'));
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

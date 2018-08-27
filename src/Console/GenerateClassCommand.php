<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Console;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;


final class GenerateClassCommand extends Command
{

	protected function configure()
	{
		$this->setName('grifart:scaffolder:generateClass')
			->setDescription('Generate a class from given definition.')
			->addArgument('definition', InputArgument::REQUIRED, 'Definition file or directory containing definitions')
			->addOption('search-pattern', NULL, InputArgument::OPTIONAL, '(for directories) Search pattern for your definitions', '*.definition.php')
			->addOption('dry-run', NULL, InputOption::VALUE_NONE, 'Only print the generated file to output instead of saving it')
			->setAliases(['doklady:scaffolder:generate' /* API used before extracted from doklady.io/invoicing-app */]);
	}


	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$definitionPath = $input->getArgument('definition');

		// 1. find files to process
		$filesToProcess = [];
		if(is_dir($definitionPath)) {
			foreach(Finder::find($input->getOption('search-pattern'))->from($definitionPath) as $definitionFile) {
				$filesToProcess[] = (string) $definitionFile;
			}
		} elseif (is_file($definitionPath)) {
			$filesToProcess[] = (string) $definitionPath;
		} else {
			$output->writeln('<error>Given path is nor a file or directory.</error>');
			return 1;
		}

		// 2. process them
		$hasError = FALSE;
		$processedFiles = 0;
		$total = count($filesToProcess);
		foreach($filesToProcess as $filesToProcess) {
			$processedFiles++;
			try {
				$this->processFile($filesToProcess, $input, $output);
				$output->write("Processed $processedFiles / $total\r");
			} catch (\Throwable $e) {
				$hasError = TRUE;
				$output->writeln(\sprintf("\n<error>%s</error>", $e->getMessage()));
			}
		}
		$output->writeln("<info>$processedFiles processed files.</info>");
		if ($hasError) {
			$output->writeln('<error>Some files wasn\'t processed correctly. See above.</error>');
			return 1;
		}
		return 0;
	}

	private function processFile(string $definitionFile, InputInterface $input, OutputInterface $output): void {
		foreach($this->loadDefinitions($definitionFile) as $definition) {
			if (!$definition instanceof ClassDefinition) {
				throw new \InvalidArgumentException('Definition file must contain class definition.');
			}
			$this->doGeneration($definition, $definitionFile, $input, $output);
		}
	}

	private function doGeneration(ClassDefinition $definition, string $definitionFile, InputInterface $input, OutputInterface $output): void {
		$classGenerator = new ClassGenerator();
		$generatedClass = $classGenerator->generateClass($definition);

		$code = '<?php declare(strict_types = 1);'
			. "\n\n"
			. \sprintf(
				\implode("\n", [
					'/**',
					' * Do not edit. This is generated file. Modify definition "%s" instead.',
					' */',
				]),
				\pathinfo($definitionFile, \PATHINFO_BASENAME)
			)
			. "\n\n"
			. $generatedClass;

		$targetPath = Path::join(
			Path::getDirectory($definitionFile),
			$definition->getClassName() . '.php'
		);
		if ($input->getOption('dry-run')) {
			echo ' ---- ' . $targetPath . " ---- \n";
			echo $code . "\n\n";

		} else {
			\file_put_contents($targetPath, $code);
		}
	}


	private function loadDefinitions(?string $definitionFile): array
	{
		$definitionFile = Path::canonicalize($definitionFile);
		if ( ! \file_exists($definitionFile)) {
			throw new \InvalidArgumentException(\sprintf(
				'<error>Definition file not found at %s</error>',
				$definitionFile
			));
		}

		$definitions = require $definitionFile;
		if (!\is_array($definitions)) {
			$definitions = [$definitions];
		}
		foreach($definitions as $definition) {
			if ( ! ($definition instanceof ClassDefinition)) {
				throw new \InvalidArgumentException(\sprintf(
					'<error>Definition file must return instanceof ClassDefinition, %s received.</error>',
					\is_object($definition) ? \get_class($definition) : \gettype($definition)
				));
			}
		}

		return $definitions;
	}

}

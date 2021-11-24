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

	protected function configure(): void
	{
		$this->setName('grifart:scaffolder:generateClass')
			->setDescription('Generate a class from given definition.')
			->addArgument('definition', InputArgument::OPTIONAL, 'Definition file or directory containing definitions', \getcwd())
			->addOption('search-pattern', NULL, InputArgument::OPTIONAL, '(for directories) Search pattern for your definitions', '*.definition.php')
			->addOption('dry-run', NULL, InputOption::VALUE_NONE, 'Only print the generated file to output instead of saving it')
			->addOption('no-readonly', NULL, InputOption::VALUE_NONE, 'Generated files are marked as read only by default (using chmod), using this option turns off this behaviour.');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$definitionPath = $input->getArgument('definition');
		$searchPattern = $input->getOption('search-pattern');
		$readonly = ! $input->getOption('no-readonly');
		\assert(\is_string($definitionPath) && \is_string($searchPattern));

		// 1. find files to process
		$filesToProcess = [];
		if(is_dir($definitionPath)) {
			foreach(Finder::find($searchPattern)->from($definitionPath) as $definitionFile) {
				$filesToProcess[] = (string) $definitionFile;
			}
		} elseif (is_file($definitionPath)) {
			$filesToProcess[] = $definitionPath;
		} else {
			$output->writeln('<error>Given path is nor a file or directory.</error>');
			return 1;
		}

		// 2. process them
		$hasError = FALSE;
		$processedFiles = 0;
		$total = count($filesToProcess);
		foreach($filesToProcess as $fileToProcess) {
			$processedFiles++;
			try {
				$this->processFile($fileToProcess, $input, $output, $readonly);
				$output->write("Processed $processedFiles / $total\r");
			} catch (\Throwable $e) {
				$hasError = TRUE;
				$output->writeln(\sprintf("\n%s: <error>%s</error>", $fileToProcess, $e->getMessage()));
				if (\class_exists(\Tracy\Debugger::class)) {
					\Tracy\Debugger::log($e);
				}
			}
		}
		$output->writeln("<info>$processedFiles processed files.</info>");
		if ($hasError) {
			$output->writeln('<error>Some files wasn\'t processed correctly. See above.</error>');
			return 1;
		}
		return 0;
	}

	private function processFile(string $definitionFile, InputInterface $input, OutputInterface $output, bool $readonly): void {
		foreach($this->loadDefinitions($definitionFile) as $definition) {
			if (!$definition instanceof ClassDefinition) {
				throw new \InvalidArgumentException('Definition file must contain class definition.');
			}
			$this->doGeneration($definition, $definitionFile, $input, $output, $readonly);
		}
	}

	private function doGeneration(ClassDefinition $definition, string $definitionFile, InputInterface $input, OutputInterface $output, bool $readonly): void {
		$classGenerator = new ClassGenerator();
		$generatedFile = $classGenerator->generateClass($definition);
		$code = (string) $generatedFile;

		$targetPath = Path::join(
			Path::getDirectory($definitionFile),
			$definition->getClassName() . '.php'
		);
		if ($input->getOption('dry-run')) {
			echo ' ---- ' . $targetPath . " ---- \n";
			echo $code . "\n\n";

		} else {
			\chmod($targetPath, 0664); // some users accessing files using group permissions
			\file_put_contents($targetPath, $code);
			if ($readonly) {
				\chmod($targetPath, 0444); // read-only -- assumes single user system
			}
		}
	}


	/**
	 * @return ClassDefinition[]
	 */
	private function loadDefinitions(string $definitionFile): iterable
	{
		$definitionFile = Path::canonicalize($definitionFile);
		if ( ! \file_exists($definitionFile)) {
			throw new \InvalidArgumentException(\sprintf(
				'<error>Definition file not found at %s</error>',
				$definitionFile
			));
		}

		$definitions = require $definitionFile;
		if ( ! \is_iterable($definitions)) {
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

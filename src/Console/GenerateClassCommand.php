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

		try {
			if(is_dir($definitionPath)) {
				foreach(Finder::find($input->getOption('search-pattern'))->from($definitionPath) as $definitionFile) {
					$this->doGeneration(
						$this->processDefinition((string) $definitionFile),
						(string) $definitionFile,
						$input,
						$output
					);
				}
				return 0;

			}

			if (is_file($definitionPath)) {
				$this->doGeneration($this->processDefinition($definitionPath), (string) $definitionPath, $input, $output);
				return 0;

			}

			// else error, see bellow

		} catch (\InvalidArgumentException $e) {
			$output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
			throw $e;
		}

		$output->writeln('<error>Given path is nor a file nor a directory.</error>');
		return 1;
	}

	private function doGeneration(ClassDefinition $definition, string $definitionFile, InputInterface $input, OutputInterface $output): void
	{
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


	private function processDefinition(?string $definitionFile): ClassDefinition
	{
		$definitionFile = Path::canonicalize($definitionFile);
		if ( ! \file_exists($definitionFile)) {
			throw new \InvalidArgumentException(\sprintf(
				'<error>Definition file not found at %s</error>',
				$definitionFile
			));
		}

		$definition = require $definitionFile;
		if ( ! ($definition instanceof ClassDefinition)) {
			throw new \InvalidArgumentException(\sprintf(
				'<error>Definition file must return instanceof ClassDefinition, %s received.</error>',
				\is_object($definition) ? \get_class($definition) : \gettype($definition)
			));
		}

		return $definition;
	}

}

<?php

declare(strict_types = 1);

namespace Doklady\Scaffolder\Console;

use Doklady\Scaffolder\ClassGenerator;
use Doklady\Scaffolder\Definition\ClassDefinition;
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
			->addArgument('definition', InputArgument::REQUIRED, 'Definition file')
			->addOption('dry-run', NULL, InputOption::VALUE_NONE, 'Only print the generated file to output instead of saving it')
			->setAliases(['doklady:scaffolder:generate' /* API used before extracted from doklady.io/invoicing-app */]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$definitionFile = $input->getArgument('definition');

		try {
			$definition = $this->processDefinition($definitionFile);

		} catch (\InvalidArgumentException $e) {
			$output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
			return 1;
		}

		$classGenerator = new ClassGenerator();
		$generatedClass = $classGenerator->generateClass($definition);

		$code = '<?php declare(strict_types = 1);'
			. "\n\n"
			. \sprintf(
				\implode("\n", [
					'/**',
					' * This file was generated from %s on %s',
					' * Do not change this file or definition, create a new version instead.',
					' */',
				]),
				\pathinfo($definitionFile, \PATHINFO_BASENAME),
				(new \DateTimeImmutable())->format(\DATE_ATOM)
			)
			. "\n\n"
			. $generatedClass;

		if ($input->getOption('dry-run')) {
			echo $code;

		} else {
			$directory = Path::getDirectory($definitionFile);
			\file_put_contents(
				Path::join(
					$directory,
					$definition->getClassName() . '.php'
				),
				$code
			);
		}

		return 0;
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

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
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\PathUtil\Path;


final class GenerateClassCommand extends Command
{
	private ClassGenerator $classGenerator;
	private SymfonyStyle $style;

	public function __construct()
	{
		parent::__construct();
		$this->classGenerator = new ClassGenerator();
	}

	protected function configure(): void
	{
		$this->setName('grifart:scaffold')
			->setDescription('Generate classes from given definitions.')
			->addArgument('definition', InputArgument::OPTIONAL, 'Definition file or directory containing definitions.', \getcwd())
			->addOption('search-pattern', NULL, InputArgument::OPTIONAL, 'Search pattern for your definition files.', '*.definition.php')
			->addOption('no-readonly', NULL, InputOption::VALUE_NONE, 'Generated files are marked as read only by default (using chmod). Use this option to turn off this behaviour.')
			->addOption('dry-run', NULL, InputOption::VALUE_NONE, 'Only print the generated file to output instead of saving it.')
			->setAliases(['grifart:scaffolder:generateClass']);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->style = new SymfonyStyle($input, $output);

		$definitionPath = $input->getArgument('definition');
		$cwd = \getcwd();
		\assert(\is_string($definitionPath) && \is_string($cwd));
		$definitionPath = Path::makeAbsolute($definitionPath, $cwd);

		$searchPattern = $input->getOption('search-pattern');
		\assert(\is_string($searchPattern));

		// 1. find files to process
		$filesToProcess = [];
		if (is_dir($definitionPath)) {
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

		/** @var FileResult[] */
		$results = [];
		$isSuccess = true;

		$processedFiles = 0;
		$total = count($filesToProcess);

		$output->writeln(\sprintf('Processing %d definition file%s:%s', $total, $total !== 1 ? 's' : '', \PHP_EOL));

		foreach ($filesToProcess as $fileToProcess) {
			$results[] = $result = $this->processFile($fileToProcess, $input);

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

		$this->printResults($results, $input, $output);

		return (int) ! $isSuccess;
	}

	private function processFile(
		string $definitionFile,
		InputInterface $input,
	): FileResult
	{
		try {
			$definitions = $this->loadDefinitions($definitionFile);
		} catch (\Throwable $error) {
			return new FileResult($definitionFile, $error);
		}

		$result = new FileResult($definitionFile, null);
		foreach ($definitions as $definition) {
			$definitionResult = $this->generateClass($definition, $definitionFile, $input);
			$result->addDefinition($definitionResult);
		}

		return $result;
	}

	private function generateClass(
		ClassDefinition $definition,
		string $definitionFile,
		InputInterface $input,
	): DefinitionResult
	{
		try {
			$generatedFile = $this->classGenerator->generateClass($definition);
		} catch (\Throwable $error) {
			return DefinitionResult::error($definition, $error);
		}

		$code = (string) $generatedFile;

		$targetPath = Path::join(
			Path::getDirectory($definitionFile),
			$definition->getClassName() . '.php'
		);

		if ($input->getOption('dry-run')) {
			return DefinitionResult::success($definition, $code);
		}

		if (\file_exists($targetPath)) {
			\chmod($targetPath, 0664); // some users accessing files using group permissions
		}

		if (\file_put_contents($targetPath, $code) === false) {
			throw new \RuntimeException('Failed to write file.');
		}

		if ( ! $input->getOption('no-readonly')) {
			\chmod($targetPath, 0444); // read-only -- assumes single user system
		}

		return DefinitionResult::success($definition, $code);
	}

	/**
	 * @return ClassDefinition[]
	 */
	private function loadDefinitions(string $definitionFile): iterable
	{
		$definitionFile = Path::canonicalize($definitionFile);
		if ( ! \file_exists($definitionFile)) {
			throw new \InvalidArgumentException(\sprintf(
				'Definition file not found at %s',
				$definitionFile
			));
		}

		$definitions = require $definitionFile;
		if ($definitions instanceof ClassDefinition) {
			$definitions = [$definitions];
		}

		if ( ! \is_iterable($definitions)) {
			throw new \InvalidArgumentException(\sprintf(
				'Definition file must return an iterable of ClassDefinition, %s received.',
				\get_debug_type($definitions),
			));
		}

		$count = 0;
		foreach ($definitions as $definition) {
			if ( ! ($definition instanceof ClassDefinition)) {
				throw new \InvalidArgumentException(\sprintf(
					'Definition file must return instanceof ClassDefinition, %s received.',
					\get_debug_type($definition),
				));
			}

			$count++;
		}

		if ($count === 0) {
			throw new \InvalidArgumentException('Definition file must return at least one ClassDefinition, empty list received.');
		}

		return $definitions;
	}

	/**
	 * @param FileResult[] $results
	 */
	private function printResults(
		array $results,
		InputInterface $input,
		OutputInterface $output,
	): void
	{
		$isDryRun = (bool) $input->getOption('dry-run');
		foreach ($results as $result) {
			if ( ! $isDryRun && $result->isSuccessful() && ! $output->isVerbose()) {
				continue;
			}

			$cwd = \getcwd();
			\assert(\is_string($cwd));
			$definitionFilePath = Path::makeRelative($result->getDefinitionFile(), $cwd);
			$definitions = $result->getDefinitions();

			$definitionFileError = $result->getError();
			if ($definitionFileError !== null) {
				$this->style->section(\sprintf(
					'%s: <error>error loading definitions</error>',
					$definitionFilePath,
				));

				$this->printError($definitionFileError, $output);

				continue;
			}

			$errors = \array_filter($definitions, static fn(DefinitionResult $result) => ! $result->isSuccessful());
			$this->style->section(\sprintf(
				'%s: %d definition%s, %s',
				$definitionFilePath,
				\count($definitions),
				\count($definitions) !== 1 ? 's' : '',
				\count($errors) === 0 ? '<info>OK</info>' : \sprintf(
					'<error>%d error%s</error>',
					\count($errors),
					\count($errors) > 1 ? 's' : '',
				),
			));

			foreach ($definitions as $definition) {
				if ( ! $isDryRun && $definition->isSuccessful() && ! $output->isVeryVerbose()) {
					continue;
				}

				$output->writeln(\sprintf(
					'%s %s%s',
					$definition->isSuccessful() ? '<info> OK </info>' : '<error>FAIL</error>',
					$definition->getClassName(),
					\PHP_EOL,
				));

				if ( ! $definition->isSuccessful()) {
					$error = $definition->getError();
					\assert($error !== null);

					$this->printError($error, $output);
					$output->writeln('');

				} elseif ($isDryRun) {
					$code = $definition->getCode();
					\assert($code !== null);

					$output->writeln('');
					$output->writeln($code . \PHP_EOL);
				}
			}
		}
	}

	private function printError(\Throwable $error, OutputInterface $output): void
	{
		$this->style->error([
			\sprintf(
				'%s: %s',
				\get_class($error),
				$error->getMessage(),
			),
			\sprintf(
				'in %s:%d',
				$error->getFile(),
				$error->getLine(),
			),
		]);

		if ( ! \class_exists(\Tracy\Debugger::class)) {
			return;
		}

		try {
			$exceptionFile = \Tracy\Debugger::log($error);
		} catch (\Throwable) {
			return;
		}

		if (\is_string($exceptionFile)) {
			$cwd = \getcwd();
			\assert(\is_string($cwd));

			$output->writeln(\sprintf(
				'Error was logged in %s',
				Path::makeRelative($exceptionFile, $cwd),
			));
		}
	}
}

final class FileResult
{
	/** @var DefinitionResult[] */
	private array $definitions = [];

	public function __construct(
		private string $definitionFile,
		private \Throwable|null $error,
	) {}

	public function getDefinitionFile(): string
	{
		return $this->definitionFile;
	}

	public function getError(): \Throwable|null
	{
		return $this->error;
	}

	public function isSuccessful(): bool
	{
		foreach ($this->definitions as $result) {
			if ( ! $result->isSuccessful()) {
				return false;
			}
		}

		return $this->error === null;
	}

	public function addDefinition(DefinitionResult $definitionResult): void
	{
		$this->definitions[] = $definitionResult;
	}

	/**
	 * @return DefinitionResult[]
	 */
	public function getDefinitions(): array
	{
		return $this->definitions;
	}
}

final class DefinitionResult
{
	private function __construct(
		private ClassDefinition $definition,
		private \Throwable|null $error,
		private string|null $code,
	) {}

	public static function success(ClassDefinition $definition, string $code): self
	{
		return new self($definition, null, $code);
	}

	public static function error(ClassDefinition $definition, \Throwable $error): self
	{
		return new self($definition, $error, null);
	}

	public function getClassName(): string
	{
		return $this->definition->getFullyQualifiedName();
	}

	public function isSuccessful(): bool
	{
		return $this->error === null;
	}

	public function getError(): \Throwable|null
	{
		return $this->error;
	}

	public function getCode(): string|null
	{
		return $this->code;
	}
}

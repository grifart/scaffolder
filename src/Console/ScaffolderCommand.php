<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Console;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\DefinitionFile;
use Grifart\ClassScaffolder\DefinitionResult;
use Grifart\ClassScaffolder\FileResult;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\PathUtil\Path;

abstract class ScaffolderCommand extends Command
{
	private const ARGUMENT_DEFINITION_PATH = 'definition';
	private const OPTION_SEARCH_PATTERN = 'search-pattern';

	protected ClassGenerator $classGenerator;
	protected SymfonyStyle $style;

	public function __construct()
	{
		parent::__construct();
		$this->classGenerator = new ClassGenerator();
	}

	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->style = new SymfonyStyle($input, $output);
	}

	protected function configure(): void
	{
		$this->addArgument(self::ARGUMENT_DEFINITION_PATH, InputArgument::OPTIONAL, 'Definition file or directory containing definitions.', \getcwd());
		$this->addOption(self::OPTION_SEARCH_PATTERN, null, InputOption::VALUE_REQUIRED, 'Search pattern for your definition files.', '*.definition.php');
	}

	/**
	 * @return DefinitionFile[]
	 */
	protected function locateDefinitionFiles(
		InputInterface $input,
	): array
	{
		$path = $this->getDefinitionPath($input);
		$searchPattern = $this->getSearchPattern($input);

		$result = [];

		if (\is_dir($path)) {
			$files = Finder::find($searchPattern)->from($path);
			foreach ($files as $file) {
				$result[] = DefinitionFile::from($file->getPathname());
			}
		} elseif (\is_file($path)) {
			$result[] = DefinitionFile::from($path);
		} else {
			throw new \RuntimeException('Given path is neither a file nor a directory.');
		}

		return $result;
	}

	protected function printError(\Throwable $error, OutputInterface $output): void
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

	/**
	 * @param FileResult[] $results
	 */
	protected function printResults(
		array $results,
		OutputInterface $output,
	): void
	{
		foreach ($results as $result) {
			if ($result->isSuccessful() && ! $output->isVerbose()) {
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
				if ($definition->isSuccessful() && ! $output->isVeryVerbose()) {
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
				}
			}
		}
	}

	private function getDefinitionPath(InputInterface $input): string
	{
		$cwd = \getcwd();

		$definitionPath = $input->getArgument(self::ARGUMENT_DEFINITION_PATH);
		\assert(\is_string($definitionPath) && \is_string($cwd));

		return Path::makeAbsolute($definitionPath, $cwd);
	}

	private function getSearchPattern(InputInterface $input): string
	{
		$searchPattern = $input->getOption(self::OPTION_SEARCH_PATTERN);
		\assert(\is_string($searchPattern));

		return $searchPattern;
	}
}

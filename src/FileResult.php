<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder;

/**
 * @internal
 */
final class FileResult
{
	/** @var DefinitionResult[] */
	private array $definitions = [];

	public function __construct(
		private DefinitionFile $definitionFile,
		private \Throwable|null $error,
	) {}

	public function getDefinitionFile(): string
	{
		return $this->definitionFile->getPath();
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

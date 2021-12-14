<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Symfony\Component\Filesystem\Path;


/**
 * @internal
 */
final class DefinitionFile
{
	private function __construct(
		private string $path,
	) {}

	public static function from(string $path): self
	{
		return new self(
			Path::canonicalize($path),
		);
	}

	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @return iterable<ClassDefinition>
	 */
	public function load(): iterable
	{
		if ( ! \file_exists($this->path) || ! \is_readable($this->path)) {
			throw new \RuntimeException(\sprintf(
				'Definition file not found or not readable at %s',
				$this->path,
			));
		}

		$definitions = require $this->path;
		if ($definitions instanceof ClassDefinition) {
			$definitions = [$definitions];
		}

		if ( ! \is_iterable($definitions)) {
			throw new \RuntimeException(\sprintf(
				'Definition file must return an iterable of ClassDefinition, %s received.',
				\get_debug_type($definitions),
			));
		}

		$count = 0;
		foreach ($definitions as $definition) {
			if ( ! ($definition instanceof ClassDefinition)) {
				throw new \RuntimeException(\sprintf(
					'Definition file must return instanceof ClassDefinition, %s received.',
					\get_debug_type($definition),
				));
			}

			$count++;
		}

		if ($count === 0) {
			throw new \RuntimeException('Definition file must return at least one ClassDefinition, empty list received.');
		}

		return $definitions;
	}

	public function resolveTargetFileFor(ClassDefinition $definition): string
	{
		return Path::join(
			Path::getDirectory($this->path),
			$definition->getClassName() . '.php',
		);
	}
}

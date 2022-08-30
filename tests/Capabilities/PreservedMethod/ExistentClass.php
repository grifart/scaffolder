<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedMethod;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;

final class ExistentClass
{
	public function preservedMethod(): mixed
	{
		return 'whatever';
	}

	public function preservedMethodWithParam(int $whatever): void {}

	/** @param iterable<int, string[]> $whatever */
	public function preservedMethodWithPhpDocParam(iterable $whatever): void {}

	public function preservedMethodWithImportedUses(ClassDefinitionBuilder $builder): string {
		return ClassDefinition::class;
	}

	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	public function preservedMethodWithAnnotation(): void {
	}

	public function methodToBeRemoved(): void
	{}
}

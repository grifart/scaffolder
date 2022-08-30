<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedAnnotatedMethods;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\Preserve;

final class ExistentClass
{
	#[Preserve]
	public function methodToBeKept(): mixed
	{
		return 'whatever';
	}

	#[Preserve]
	public function methodToBeKeptWithParam(int $whatever): void {}

	/** @param iterable<int, string[]> $whatever */
	#[Preserve]
	public function methodToBeKeptWithPhpDocParam(iterable $whatever): void {}

	#[Preserve]
	public function methodToBeKeptWithImportedUses(ClassDefinitionBuilder $builder): string {
		return ClassDefinition::class;
	}

	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	#[Preserve]
	public function methodToBeKeptWithAnnotation(): void {
	}

	public function methodToBeRemoved(): void
	{}
}

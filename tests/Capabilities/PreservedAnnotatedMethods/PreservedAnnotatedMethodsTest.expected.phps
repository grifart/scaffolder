<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedAnnotatedMethods;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\Preserve;

final class ExistentClass
{
	#[Preserve]
	public function preservedMethod(): mixed
	{
		return 'whatever';
	}


	#[Preserve]
	public function preservedMethodWithParam(int $whatever): void
	{
	}


	/**
	 * @param iterable<int, string[]> $whatever
	 */
	#[Preserve]
	public function preservedMethodWithPhpDocParam(iterable $whatever): void
	{
	}


	#[Preserve]
	public function preservedMethodWithImportedUses(ClassDefinitionBuilder $builder): string
	{
		return ClassDefinition::class;
	}


	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	#[Preserve]
	public function preservedMethodWithAnnotation(): void
	{
	}
}

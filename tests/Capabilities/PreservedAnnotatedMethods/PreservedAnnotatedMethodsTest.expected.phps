<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Capabilities\PreservedAnnotatedMethods;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\KeepMethod;

final class ExistentClass
{
	#[KeepMethod]
	public function methodToBeKept(): mixed
	{
		return 'whatever';
	}


	#[KeepMethod]
	public function methodToBeKeptWithParam(int $whatever): void
	{
	}


	/**
	 * @param iterable<int, string[]> $whatever
	 */
	#[KeepMethod]
	public function methodToBeKeptWithPhpDocParam(iterable $whatever): void
	{
	}


	#[KeepMethod]
	public function methodToBeKeptWithImportedUses(ClassDefinitionBuilder $builder): string
	{
		return ClassDefinition::class;
	}


	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	#[KeepMethod]
	public function methodToBeKeptWithAnnotation(): void
	{
	}
}

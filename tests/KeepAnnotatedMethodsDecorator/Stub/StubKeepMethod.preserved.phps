<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\KeepAnnotatedMethodsDecorator\Stub;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\KeepMethod;

final class StubKeepMethod
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


	#[KeepMethod]
	public function methodToBeKeptWithMixedParam(mixed $whatever): void
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

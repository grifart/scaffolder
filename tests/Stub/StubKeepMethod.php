<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Stub;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;


final class StubKeepMethod
{

	public function methodToBeKept(): mixed
	{
		return 'whatever';
	}

	public function methodToBeKeptWithParam(int $whatever): void {}

	public function methodToBeKeptWithImportedUses(ClassDefinitionBuilder $builder): string {
		return ClassDefinition::class;
	}

	/**
	 * Php doc!
	 * @return void
	 * @throws \Throwable
	 */
	public function methodToBeKeptWithAnnotation(): void {
	}

	public function methodToBeRemoved(): void
	{}

}

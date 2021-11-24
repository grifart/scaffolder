<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Definition;

use Grifart\ClassScaffolder\Capabilities\ImplementedInterface;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\Definition\Types\SimpleType;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class ClassDefinitionBuilderTest extends TestCase
{
	public function testBuilder(): void
	{
		$errorReporting = error_reporting(~E_USER_DEPRECATED);
		$definition = (new ClassDefinitionBuilder('RootNamespace\SubNamespace\ClassName'))
			->implement(\Iterator::class)
			->field('field', 'string')
			->decorate(new PropertiesDecorator())
			->build();
		error_reporting($errorReporting);

		Assert::same('RootNamespace\SubNamespace\ClassName', $definition->getFullyQualifiedName());
		Assert::same('RootNamespace\SubNamespace', $definition->getNamespaceName());
		Assert::same('ClassName', $definition->getClassName());

		Assert::count(1, $definition->getFields());
		Assert::same('field', $definition->getFields()[0]->getName());
		Assert::type(SimpleType::class, $definition->getFields()[0]->getType());
		Assert::type('string', $definition->getFields()[0]->getType()->getTypeHint());

		Assert::count(2, $definition->getCapabilities());
		Assert::type(ImplementedInterface::class, $definition->getCapabilities()[0]);
		Assert::type(PropertiesDecorator::class, $definition->getCapabilities()[1]);
	}

	public function testDeprecation(): void
	{
		Assert::error(
			fn() => new ClassDefinitionBuilder('RootNamespace\SubNamespace\ClassName'),
			\E_USER_DEPRECATED,
			'ClassDefinitionBuilder is deprecated, use ClassDefinition directly instead.',
		);
	}
}

(new ClassDefinitionBuilderTest())->run();

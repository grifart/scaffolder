<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test\Definition;

use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types\CheckedClassType;
use Grifart\ClassScaffolder\Definition\Types\NullableType;
use Grifart\ClassScaffolder\Definition\Types\SimpleType;
use Tester\Assert;
use Tester\TestCase;
use function Grifart\ClassScaffolder\Definition\definitionOf;
use function Grifart\ClassScaffolder\Definition\Types\nullable;
use function Grifart\ClassScaffolder\Definition\Types\resolve;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class ClassDefinitionTest extends TestCase
{
	public function testNameWithoutNamespace(): void
	{
		$definition = new ClassDefinition('ClassName');
		Assert::same('ClassName', $definition->getFullyQualifiedName());
		Assert::null($definition->getNamespaceName());
		Assert::same('ClassName', $definition->getClassName());
	}

	public function testNameWithNamespace(): void
	{
		$definition = new ClassDefinition('RootNamespace\SubNamespace\ClassName');
		Assert::same('RootNamespace\SubNamespace\ClassName', $definition->getFullyQualifiedName());
		Assert::same('RootNamespace\SubNamespace', $definition->getNamespaceName());
		Assert::same('ClassName', $definition->getClassName());
	}

	public function testFields(): void
	{
		$definition = (new ClassDefinition('ClassName'))->withFields(['field1' => resolve('string'), 'field2' => nullable('int')]);
		Assert::count(2, $definition->getFields());
		Assert::same('field1', $definition->getFields()[0]->getName());
		Assert::type(SimpleType::class, $definition->getFields()[0]->getType());
		Assert::same('field2', $definition->getFields()[1]->getName());
		Assert::type(NullableType::class, $definition->getFields()[1]->getType());

		$updatedDefinition = $definition->withField('field3', resolve(\Iterator::class));
		Assert::count(3, $updatedDefinition->getFields());
		Assert::same('field1', $updatedDefinition->getFields()[0]->getName());
		Assert::type(SimpleType::class, $updatedDefinition->getFields()[0]->getType());
		Assert::same('field2', $updatedDefinition->getFields()[1]->getName());
		Assert::type(NullableType::class, $updatedDefinition->getFields()[1]->getType());
		Assert::same('field3', $updatedDefinition->getFields()[2]->getName());
		Assert::type(CheckedClassType::class, $updatedDefinition->getFields()[2]->getType());

		Assert::count(2, $definition->getFields());
		Assert::same('field1', $definition->getFields()[0]->getName());
		Assert::type(SimpleType::class, $definition->getFields()[0]->getType());
		Assert::same('field2', $definition->getFields()[1]->getName());
		Assert::type(NullableType::class, $definition->getFields()[1]->getType());
	}

	public function testCapabilities(): void
	{
		$capability1 = new class implements Capability {
			public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void {}
		};
		$capability2 = new class implements Capability {
			public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void {}
		};
		$capability3 = new class implements Capability {
			public function applyTo(ClassDefinition $definition, ClassInNamespace $draft, ?ClassInNamespace $current): void {}
		};

		$definition = (new ClassDefinition('ClassName'))->with($capability1, $capability2);
		Assert::same([$capability1, $capability2], $definition->getCapabilities());

		$updatedDefinition = $definition->with($capability3);
		Assert::same([$capability1, $capability2, $capability3], $updatedDefinition->getCapabilities());
		Assert::same([$capability1, $capability2], $definition->getCapabilities());
	}

	public function testDefinitionOf(): void
	{
		$definition = definitionOf('ClassName');
		Assert::same('ClassName', $definition->getFullyQualifiedName());
		Assert::count(0, $definition->getFields());

		$updatedDefinition = definitionOf($definition, withFields: ['field' => resolve('string')]);
		Assert::same('ClassName', $updatedDefinition->getFullyQualifiedName());
		Assert::count(1, $updatedDefinition->getFields());
		Assert::count(0, $definition->getFields());
	}
}

(new ClassDefinitionTest())->run();

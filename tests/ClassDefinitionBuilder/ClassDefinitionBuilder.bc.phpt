<?php declare(strict_types=1);

use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(function () {
	Assert::error(static fn () => new ClassDefinitionBuilder('BuiltClassName'), E_USER_DEPRECATED);

	error_reporting(~E_USER_DEPRECATED);

	$definition = (new ClassDefinitionBuilder('BuiltClassName'))
		->implement(Iterator::class)
		->field('field', Types\resolve('mixed'))
		->decorate(new PropertiesDecorator())
		->build();

	Assert::type(ClassDefinition::class, $definition);
	Assert::same(BuiltClassName::class, $definition->getFullyQualifiedName());
	Assert::same([Iterator::class], $definition->getImplements());
	Assert::count(1, $definition->getFields());
	Assert::same('field', $definition->getFields()[0]->getName());
	Assert::type(Types\SimpleType::class, $definition->getFields()[0]->getType());
	Assert::same('mixed', $definition->getFields()[0]->getType()->getTypeHint());
	Assert::count(1, $definition->getDecorators());
	Assert::type(PropertiesDecorator::class, $definition->getDecorators()[0]);
});

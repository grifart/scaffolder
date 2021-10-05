<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\Decorators\GettersDecorator;
use Grifart\ClassScaffolder\Definition\Types\SimpleType;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$builder = \Grifart\ClassScaffolder\Definition\define(BuiltClass::class);
$builder->field('field', 'string');
$builder->decorate(new GettersDecorator());
$builder->implement(\Countable::class);
$definition = $builder->build();

Assert::same(BuiltClass::class, $definition->getFullyQualifiedName());
Assert::same(__NAMESPACE__, $definition->getNamespaceName());
Assert::same('BuiltClass', $definition->getClassName());

Assert::count(1, $definition->getFields());
Assert::same('field', $definition->getFields()[0]->getName());
Assert::type(SimpleType::class, $definition->getFields()[0]->getType());
Assert::same('string', $definition->getFields()[0]->getType()->getTypeHint());

Assert::count(1, $definition->getDecorators());
Assert::type(GettersDecorator::class, $definition->getDecorators()[0]);

Assert::count(1, $definition->getImplements());
Assert::same(\Countable::class, $definition->getImplements()[0]);

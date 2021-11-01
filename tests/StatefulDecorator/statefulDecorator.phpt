<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Decorators\ConstructorWithPromotedPropertiesDecorator;
use Grifart\ClassScaffolder\Decorators\KeepAnnotatedMethodsDecorator;
use Grifart\ClassScaffolder\Decorators\KeepUseStatementsDecorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Decorators\StatefulDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\ClassScaffolder\Definition\Types;
use Grifart\Stateful\Stateful;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
$generator = new ClassGenerator();

$generateClass = static fn () => $generator->generateClass(
	new ClassDefinition(
		'Grifart\ClassScaffolder\Test',
		'StatefulDecorator',
		[Stateful::class],
		[new Field('field', Types\resolve('string'))],
		[
			new ConstructorWithPromotedPropertiesDecorator(),
			new StatefulDecorator(),
		],
	),
);

// uses are copied from current
Assert::matchFile(
	__DIR__ . '/expected.phps',
	(string) $generateClass(),
);

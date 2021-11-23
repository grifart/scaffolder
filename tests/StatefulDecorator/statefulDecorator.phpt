<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Grifart\Stateful\Stateful;
use Tester\Assert;
use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\implementedInterface;
use function Grifart\ClassScaffolder\Capabilities\statefulImplementation;

require_once __DIR__ . '/../bootstrap.php';
$generator = new ClassGenerator();

$generateClass = static fn () => $generator->generateClass(
	(new ClassDefinition('Grifart\ClassScaffolder\Test\StatefulDecorator'))
		->withField('field', Types\resolve('string'))
		->with(constructorWithPromotedProperties(), implementedInterface(Stateful::class), statefulImplementation())
);

// uses are copied from current
Assert::matchFile(
	__DIR__ . '/expected.phps',
	(string) $generateClass(),
);

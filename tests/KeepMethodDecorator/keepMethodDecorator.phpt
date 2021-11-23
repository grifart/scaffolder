<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;
use function Grifart\ClassScaffolder\Capabilities\preservedMethod;
use function Grifart\ClassScaffolder\Capabilities\preservedUseStatements;

require_once __DIR__ . '/../bootstrap.php';
$generator = new \Grifart\ClassScaffolder\ClassGenerator();

/**
 * @param ClassDecorator[] $decorators
 */
$generateClass = static fn (array $capabilities) => $generator->generateClass(
	(new ClassDefinition('Grifart\ClassScaffolder\Test\KeepMethodDecorator\Stub\StubKeepMethod'))
		->withField('field', Types\resolve('mixed'))
		->with(preservedUseStatements(), ...$capabilities)
);

// methods are preserved
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.preserved.phps',
	(string) $generateClass([
		preservedMethod('newMethod'),
		preservedMethod('methodToBeKept'),
		preservedMethod('methodToBeKeptWithParam'),
		preservedMethod('methodToBeKeptWithMixedParam'),
		preservedMethod('methodToBeKeptWithImportedUses'),
		preservedMethod('methodToBeKeptWithAnnotation'),
	]),
);

// methods are overwritten
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.overwritten.phps',
	(string) $generateClass([]),
);

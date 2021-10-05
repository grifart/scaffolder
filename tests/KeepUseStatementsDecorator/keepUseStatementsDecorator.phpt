<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Decorators\KeepAnnotatedMethodsDecorator;
use Grifart\ClassScaffolder\Decorators\KeepUseStatementsDecorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
$generator = new ClassGenerator();

$generateClass = static fn (string $className) => $generator->generateClass(
	(new ClassDefinition("Grifart\ClassScaffolder\Test\KeepUseStatementsDecorator\Stub\\$className"))
		->withField('field', Types\resolve(Field::class))
		->decoratedBy(new KeepUseStatementsDecorator(), new PropertiesDecorator())
);

// uses are copied from current
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepUses.fromCurrent.phps',
	(string) $generateClass('StubKeepUsesFromCurrent'),
);

// no current class to copy uses from
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepUses.noCurrent.phps',
	(string) $generateClass('StubKeepUsesNoCurrent'),
);

// class does not exist yet
Assert::noError(function () use ($generateClass) {
	$generateClass('NonExistentClass');
});

<?php declare(strict_types=1);

namespace Grifart\ClassScaffolder\Test;

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Decorators\ClassDecorator;
use Grifart\ClassScaffolder\Decorators\KeepAnnotatedMethodsDecorator;
use Grifart\ClassScaffolder\Decorators\KeepUseStatementsDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
$generator = new ClassGenerator();

/**
 * @param ClassDecorator[] $decorators
 */
$generateClass = static fn (array $decorators) => $generator->generateClass(
	(new ClassDefinition('Grifart\ClassScaffolder\Test\KeepAnnotatedMethodsDecorator\Stub\StubKeepMethod'))
		->withField('field', Types\resolve('mixed'))
		->decoratedBy(new KeepUseStatementsDecorator(), ...$decorators)
);

// methods are preserved
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.preserved.phps',
	(string) $generateClass([
		new KeepAnnotatedMethodsDecorator(),
	]),
);

// methods are overwritten
Assert::matchFile(
	__DIR__ . '/Stub/StubKeepMethod.overwritten.phps',
	(string) $generateClass([]),
);

// class does not exist yet
Assert::noError(function () use ($generator) {
	$generator->generateClass(
		(new ClassDefinition('Grifart\ClassScaffolder\Test\KeepAnnotatedMethodsDecorator\NonExistentClass'))
			->withField('field', Types\resolve('mixed'))
			->decoratedBy(new KeepAnnotatedMethodsDecorator())
	);
});

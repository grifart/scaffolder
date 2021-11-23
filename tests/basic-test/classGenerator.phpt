<?php declare(strict_types=1);
/**
 * @testCase
 */

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Decorators\StatefulDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\ClassDefinitionBuilder;
use \Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;
use Tester\TestCase;
use function Grifart\ClassScaffolder\Capabilities\constructorWithPromotedProperties;
use function Grifart\ClassScaffolder\Capabilities\getters;
use function Grifart\ClassScaffolder\Capabilities\implementedInterface;
use function Grifart\ClassScaffolder\Capabilities\initializingConstructor;
use function Grifart\ClassScaffolder\Capabilities\properties;
use function Grifart\ClassScaffolder\Capabilities\readonlyProperties;
use function Grifart\ClassScaffolder\Capabilities\setters;
use function Grifart\ClassScaffolder\Definition\Types\nullable;

require_once __DIR__ . '/../bootstrap.php';



(new class extends TestCase {

	private ClassGenerator $generator;

	protected function setUp()
	{
		parent::setUp();
		$this->generator = new ClassGenerator();
	}

	private function getCapabilities(): array {
		return [
			properties(),
			initializingConstructor(),
			getters(),
		];
	}

	public function dataProvider_generator(): Generator {
		yield [
			'classGenerator.1-simple.phps',
			(new ClassDefinition('NS\\CLS'))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.2-with-iterator.phps',
			(new ClassDefinition('NS\\CLS'))
				->with(implementedInterface(Iterator::class), ...$this->getCapabilities()),
		];

		yield [
			'classGenerator.3-with-field.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\resolve('mixed'))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.4-with-field-nullable.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\nullable(Types\resolve('string')))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.5-with-list.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\listOf('string'))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.6-with-complex-collection.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\collection(SplObjectStorage::class,ClassDefinition::class, SplFixedArray::class))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.7-setters.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('poem', nullable(Types\resolve('string')))
				->with(properties(), setters()),
		];

		yield [
			'classGenerator.8-generics.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('generic', Types\generic(Types\classType('NS\GenericClass'), 'int', 'callable', '?string'))
				->with(properties()),
		];

		yield [
			'classGenerator.9-cross-reference.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\resolve(new ClassDefinition('NS\\SubCLS')))
				->with(...$this->getCapabilities()),
		];

		yield [
			'classGenerator.10-promoted-properties.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('field', Types\resolve('string'))
				->with(constructorWithPromotedProperties(), getters()),
		];

		yield [
			'classGenerator.11-union.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('union', Types\union(Types\classType('NS\GenericClass'), 'int', 'callable', 'string', 'null'))
				->with(properties()),
		];

		yield [
			'classGenerator.11-union-with-generics.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('union', Types\generic('array', 'string', Types\union(Types\classType('NS\GenericClass'), 'int', 'callable', 'string', 'null')))
				->with(properties()),
		];

		yield [
			'classGenerator.12-shape.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('shape', Types\arrayShape([
					'foo' => 'int',
					'bar' => Types\generic(Types\classType('NS\GenericClass'), 'string'),
					'baz' => Types\listOf('string'),
				]))
				->with(properties()),
		];

		yield [
			'classGenerator.13-intersection.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('intersection', Types\intersection('Countable', 'Traversable'))
				->with(properties()),
		];

		yield [
			'classGenerator.14-readonly.phps',
			(new ClassDefinition('NS\\CLS'))
				->withField('answer', Types\resolve('int'))
				->with(constructorWithPromotedProperties(), readonlyProperties()),
		];

		$errorReporting = error_reporting(~E_USER_DEPRECATED);
		yield [
			'classGenerator.15-builder-bc-api.phps',
			(new ClassDefinitionBuilder('NS\\CLS'))
				->implement(Iterator::class)
				->field('field', 'mixed')
				->decorate(new PropertiesDecorator())
				->build(),
		];
		error_reporting($errorReporting);
	}

	/** @dataProvider dataProvider_generator */
	public function testGenerator(string $assertionFile, ClassDefinition $definition) {
		Assert::matchFile(
			$assertionFile,
			(string) $this->generator->generateClass($definition)
		);
	}



	public function dataProvider_capabilitiesSafety(): array {
		$classWithCapabilities = function (array $capabilities): ClassDefinition {
			return (new ClassDefinition('NS\\CLS'))
				->withField('field', Types\resolve('string'))
				->with(...$capabilities);
		};

		return [
			[$classWithCapabilities([initializingConstructor()])],
			[$classWithCapabilities([getters()])],
			[$classWithCapabilities([setters()])],
			[$classWithCapabilities([new \Grifart\ClassScaffolder\Capabilities\Decorator(new StatefulDecorator())])],
		];
	}

	/** @dataProvider dataProvider_capabilitiesSafety */
	public function testCapabilitiesSafety(ClassDefinition $definition) {
		Assert::exception(function() use ($definition) {
			$this->generator->generateClass($definition);
		}, InvalidArgumentException::class, 'Used capability requires you to have all fields specified in class specification already declared in generated class. Maybe you want to use properties() before this one?');
	}


	public function testAccessingBuiltInClass() {
		Assert::exception(function () {
			$this->generator->generateClass(
				new ClassDefinition('DateTime')
			);
		}, \LogicException::class, 'Cannot copy from core or extension class DateTime');
	}


})->run();

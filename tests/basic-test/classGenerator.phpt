<?php declare(strict_types=1);
/**
 * @testCase
 */

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use \Grifart\ClassScaffolder\Definition\Types;
use function Grifart\ClassScaffolder\Definition\Types\nullable;

require_once __DIR__ . '/../bootstrap.php';



(new class extends \Tester\TestCase {

	/** @var \Grifart\ClassScaffolder\ClassGenerator */
	private $generator;

	protected function setUp()
	{
		parent::setUp();
		$this->generator = new \Grifart\ClassScaffolder\ClassGenerator();
	}

	private function getDecorators(): array {
		return [
			new \Grifart\ClassScaffolder\Decorators\PropertiesDecorator(),
			new \Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator(),
			new \Grifart\ClassScaffolder\Decorators\GettersDecorator(),
		];
	}

	public function dataProvider_generator():array {
		return [
			[
				'classGenerator.1-simple.phps',
				new ClassDefinition('NS', 'CLS', [], [], $this->getDecorators())
			],
			[
				'classGenerator.2-with-iterator.phps',
				new ClassDefinition('NS', 'CLS', [Iterator::class], [], $this->getDecorators())
			],
			[
				'classGenerator.3-with-field.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\resolve('mixed')], $this->getDecorators())
			],
			[
				'classGenerator.4-with-field-nullable.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\nullable(Types\resolve('string'))], $this->getDecorators())
			],
			[
				'classGenerator.5-with-list.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\listOf('string')], $this->getDecorators())
			],
			[
				'classGenerator.6-with-complex-collection.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\collection(SplObjectStorage::class,ClassDefinition::class, SplFixedArray::class)], $this->getDecorators())
			],
			[
				'classGenerator.7-setters.phps',
				new ClassDefinition('NS', 'CLS', [], ['poem'=>nullable(Types\resolve('string'))], [new \Grifart\ClassScaffolder\Decorators\PropertiesDecorator(), new \Grifart\ClassScaffolder\Decorators\SettersDecorator()])
			],
			[
				'classGenerator.8-generics.phps',
				new ClassDefinition('NS', 'CLS', [], ['generic'=>Types\generic(Types\classType('NS\GenericClass'), 'int', 'callable', '?string')], [new \Grifart\ClassScaffolder\Decorators\PropertiesDecorator()])
			],
			[
				'classGenerator.9-cross-reference.phps',
				new ClassDefinition('NS', 'CLS', [], ['field'=>Types\resolve(new ClassDefinition('NS', 'SubCLS', [], [], []))], $this->getDecorators())
			],
		];
	}

	/** @dataProvider dataProvider_generator */
	public function testGenerator(string $assertionFile, ClassDefinition $definition) {
		\Tester\Assert::matchFile(
			$assertionFile,
			(string) $this->generator->generateClass($definition)
		);
	}



	public function dataProvider_decoratorsSafety(): array {
		$classWithDecorator = function (array $decorators): ClassDefinition {
			return new ClassDefinition('NS', 'CLS', [], ['field'=>Types\resolve('string')], $decorators);
		};

		return [
			[$classWithDecorator([new \Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator()])],
			[$classWithDecorator([new \Grifart\ClassScaffolder\Decorators\GettersDecorator()])],
			[$classWithDecorator([new \Grifart\ClassScaffolder\Decorators\SettersDecorator()])],
			[$classWithDecorator([new \Grifart\ClassScaffolder\Decorators\StatefulDecorator()])],
		];
	}

	/** @dataProvider dataProvider_decoratorsSafety */
	public function testDecoratorsSafety(ClassDefinition $definition) {
		\Tester\Assert::exception(function() use ($definition) {
			$this->generator->generateClass($definition);
		}, InvalidArgumentException::class, 'Used decorator requires you to have all fields specified in class specification already declared in generated class. Maybe you want to use PropertiesDecorator before this one?');
	}


})->run();

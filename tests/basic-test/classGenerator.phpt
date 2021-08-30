<?php declare(strict_types=1);
/**
 * @testCase
 */

use Grifart\ClassScaffolder\ClassGenerator;
use Grifart\ClassScaffolder\Decorators\ConstructorWithPromotedPropertiesDecorator;
use Grifart\ClassScaffolder\Decorators\GettersDecorator;
use Grifart\ClassScaffolder\Decorators\InitializingConstructorDecorator;
use Grifart\ClassScaffolder\Decorators\PropertiesDecorator;
use Grifart\ClassScaffolder\Decorators\SettersDecorator;
use Grifart\ClassScaffolder\Decorators\StatefulDecorator;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Grifart\ClassScaffolder\Definition\Field;
use \Grifart\ClassScaffolder\Definition\Types;
use Tester\Assert;
use Tester\TestCase;
use function Grifart\ClassScaffolder\Definition\Types\nullable;

require_once __DIR__ . '/../bootstrap.php';



(new class extends TestCase {

	/** @var ClassGenerator */
	private $generator;

	protected function setUp()
	{
		parent::setUp();
		$this->generator = new ClassGenerator();
	}

	private function getDecorators(): array {
		return [
			new PropertiesDecorator(),
			new InitializingConstructorDecorator(),
			new GettersDecorator(),
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
				new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\resolve('mixed'))], $this->getDecorators())
			],
			[
				'classGenerator.4-with-field-nullable.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\nullable(Types\resolve('string')))], $this->getDecorators())
			],
			[
				'classGenerator.5-with-list.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\listOf('string'))], $this->getDecorators())
			],
			[
				'classGenerator.6-with-complex-collection.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\collection(SplObjectStorage::class,ClassDefinition::class, SplFixedArray::class))], $this->getDecorators())
			],
			[
				'classGenerator.7-setters.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('poem',nullable(Types\resolve('string')))], [new PropertiesDecorator(), new SettersDecorator()])
			],
			[
				'classGenerator.8-generics.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('generic',Types\generic(Types\classType('NS\GenericClass'), 'int', 'callable', '?string'))], [new PropertiesDecorator()])
			],
			[
				'classGenerator.9-cross-reference.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\resolve(new ClassDefinition('NS', 'SubCLS', [], [], [])))], $this->getDecorators())
			],
			[
				'classGenerator.10-promoted-properties.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('field', Types\resolve('string'))], [new ConstructorWithPromotedPropertiesDecorator(), new GettersDecorator()])
			],
			[
				'classGenerator.11-union.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('union', Types\union(Types\classType('NS\GenericClass'), 'int', 'callable', 'string', 'null'))], [new PropertiesDecorator()])
			],
			[
				'classGenerator.11-union-with-generics.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('union', Types\generic('array', 'string', Types\union(Types\classType('NS\GenericClass'), 'int', 'callable', 'string', 'null')))], [new PropertiesDecorator()])
			],
			[
				'classGenerator.12-shape.phps',
				new ClassDefinition('NS', 'CLS', [], [new Field('shape', Types\arrayShape([
					'foo' => 'int',
					'bar' => Types\generic(Types\classType('NS\GenericClass'), 'string'),
					'baz' => Types\listOf('string'),
				]))], [new PropertiesDecorator()])
			],
		];
	}

	/** @dataProvider dataProvider_generator */
	public function testGenerator(string $assertionFile, ClassDefinition $definition) {
		Assert::matchFile(
			$assertionFile,
			(string) $this->generator->generateClass($definition)
		);
	}



	public function dataProvider_decoratorsSafety(): array {
		$classWithDecorator = function (array $decorators): ClassDefinition {
			return new ClassDefinition('NS', 'CLS', [], [new Field('field',Types\resolve('string'))], $decorators);
		};

		return [
			[$classWithDecorator([new InitializingConstructorDecorator()])],
			[$classWithDecorator([new GettersDecorator()])],
			[$classWithDecorator([new SettersDecorator()])],
			[$classWithDecorator([new StatefulDecorator()])],
		];
	}

	/** @dataProvider dataProvider_decoratorsSafety */
	public function testDecoratorsSafety(ClassDefinition $definition) {
		Assert::exception(function() use ($definition) {
			$this->generator->generateClass($definition);
		}, InvalidArgumentException::class, 'Used decorator requires you to have all fields specified in class specification already declared in generated class. Maybe you want to use PropertiesDecorator before this one?');
	}


})->run();

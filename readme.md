# grifart/scaffolder – The (class) scaffolder project.

It was designed to generated classes with none to simple logic. Typical usage is:

- data transfer objects (DTOs),
- events in event-sourced model,
- simple value objects (simple logic can be embedded using `#[KeepMethod]` attribute – see below).

## Installation

We recommend to use Composer:

```bash
composer require grifart/scaffolder 
```

## Quick start

1. **Create a definition file.** Definition file must return a list of `ClassDefinition`s. By default, its name must end with `.definition.php`. We commonly use just `.definition.php`:

  ```php
  <?php

  use Grifart\ClassScaffolder\Capabilities;
  use Grifart\ClassScaffolder\Definition\definitionOf;
  use Grifart\ClassScaffolder\Definition\Types;

  return [
      definitionOf(Article::class, withFields: [
          'id' => 'int',
          'title' => 'string',
          'content' => 'string',
          'tags' => Types\listOf('string'),
      ])
          ->withField('arhivedAt', Types\nullable(\DateTime::class))
          ->with(
              Capabilities\constructorWithPromotedProperties(),
              Capabilities\getters(),
          )
  ];
  ```

3. **Run scaffolder.** You can provide the path to the definition file (or a directory which is then recursively searched for definition files) as an argument. It defaults to the current working directory if omitted.

  The recommended way is to run the pre-packaged Composer binary:

  ```sh
  composer run scaffold .definition.php
  ```

<details>
  <summary>Alternative way: Register scaffolder as an Symfony command into you app.</summary>

  Alternatively, you can register the `Grifart\ClassScaffolder\Console\GenerateClassCommand` into your application's DI container and run scaffolder through *symfony/console*. This makes it easier to access your project's services and environment in definition files. *This is considered advanced usage and is not necessary in most cases.*
  
  ```sh
  php bin/console grifart:scaffold .definition.php
  ```
</details>

4. **Your class is ready.** Scaffolder generates classes from definitions, one class per file, residing in the same directory as the definition file. By default, scaffolder makes the file read-only to prevent you from changing it accidentally.

  ```php
  <?php

  /**
   * Do not edit. This is generated file. Modify definition file instead.
   */

  declare(strict_types=1);

  final class Article
  {
      /**
       * @param string[] $tags
       */
      public function __construct(
          private int $id,
          private string $title,
          private string $content,
          private array $tags,
          private ?\DateTime $archivedAt,
      ) {
      }


      public function getId(): int
      {
          return $this->id;
      }


      public function getTitle(): string
      {
          return $this->title;
      }


      public function getContent(): string
      {
          return $this->content;
      }


      /**
       * @return string[]
       */
      public function getTags(): array
      {
          return $this->tags;
      }


      public function getArchivedAt(): ?\DateTime
      {
          return $this->archivedAt;
      }
  }
  ```

5. **Use static analysis tool** such as PHPStan or Psalm to make sure that everything still works fine if you've changed any definition file.


## Definition files

A definition file must return a list of `ClassDefinition`s. The easiest way to create a definition is to use the `definitionOf()` function:

```php
<?php

    use Grifart\ClassScaffolder\Capabilities;
    use Grifart\ClassScaffolder\Definition\definitionOf;
    use Grifart\ClassScaffolder\Definition\Types;

    return [
        definitionOf(Article::class, withFields: [
            'id' => 'int',
            'title' => 'string',
            'content' => 'string',
            'tags' => Types\listOf('string'),
        ])
            ->withField('arhivedAt', Types\nullable(\DateTime::class))
            ->with(
                Capabilities\constructorWithPromotedProperties(),
                Capabilities\getters(),
            )
    ];
```

The `definitionOf()` accepts the name of the generated class and optionally a map of its fields and their types, and returns a `ClassDefinition`. You can further add fields and capabilities to the definition.

### Fields and types

Since scaffolder is primarily designed to generate various data transfer objects, fields are first-class citizens. Every field must have a type: scaffolder has an abstraction over PHP types and provides functions to compose even the most complex of types. It adds phpdoc type annotations where necessary so that static analysis tools can perfectly understand your code.

The available types are:

- **simple types** such as `'int'`, `'string'`, `'array'`, etc.

  ```php
  $definition->withField('field', 'string')
  ```

  results in

  ```php
  private string $field;
  ```

- **class references** via `::class` are resolved to the referenced class, interface or enum:

  ```php
  $definition->withField('field', \Iterator::class)
  ```

  results in

  ```php
  private Iterator $field;
  ```

- **references to other definitions** are supported and resolved:

  ```php
  $otherDefinition = definitionOf(OtherClass::class);
  $definition->withField('field', $otherDefinition);
  ```

  results in

  ```php
  private OtherClass $field;
  ```

- **nullability** can be expressed via `nullable()`:

  ```php
  $definition->withField('field', Types\nullable('string'))
  ```

  results in

  ```php
  private ?string $field;
  ```

- **lists** can be created via `listOf()`:

  ```php
  $definition->withField('field', Types\listOf('string'))
  ```

  results in

  ```php
  /** @var string[] */
  private array $field;
  ```

- **key-value collections** can be created via `collection()`:

  ```php
  $definition->withField('field', Types\collection(Collection::class, UserId::class, User::class))
  ```

  results in

  ```php
  /** @var Collection<UserId, User> */
  private Collection $field;
  ```

- **any generic types** can be represented via `generic()`:

  ```php
  $definition->withField('field', Types\generic(\SerializedValue::class, User::class))
  ```

  results in

  ```php
  /** @var SerializedValue<User> */
  private SerializedValue $field;
  ```

- **complex array shapes** can be described via `arrayShape()`:

  ```php
  $definition->withField('field', Types\arrayShape(['key' => 'string', 'optional?' => 'int']))
  ```

  results in

  ```php
  /** @var array{key: string, optional?: int} */
  private array $field;
  ```

- **similarly, tuples** can be created via `tuple()`:

  ```php
  $definition->withField('field', Types\tuple('string', Types\nullable('int')))
  ```

  results in

  ```php
  /** @var array{string, int|null} */
  private array $field;
  ```

- **unions and intersections** are supported as well:

  ```php
  $definition->withField('field', Types\union('int', 'string'))
             ->withField('other', Types\intersection(\Traversable::class, \Countable::class))
  ```

  results in

  ```php
  private int|string $field;
  private Traversable&Countable $other;
  ```

### Capabilities

Fields on their own are not represented in the generated code, they just describe which fields the resulting class should contain. To add any behaviour to the class, you need to add capabilities to it. Scaffolder comes prepared with a bundle of capabilities for the most common use-cases:

- **`properties()`** generates a private property for each field:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\properties())
  ```

  results in:

  ```php
  final class Foo
  {
      private string $field;
  }
  ```

- `initializingConstructor()` generates a public constructor with property assignments. This works best when combined with the `properties()` capability:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\properties())
      ->with(Capabilities\initializingConstructor())
  ```

  results in:

  ```php
  final class Foo
  {
      private string $field;

      public function __construct(string $field)
      {
          $this->field = $field;
      }
  }
  ```

- `constructorWithPromotedProperties()` generates a public constructor with promoted properties. This can be used instead of the preceding two capabilities in PHP 8+ code:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
  ```

  results in:

  ```php
  final class Foo
  {
      public function __construct(private string $field)
      {
      }
  }
  ```

- **`readonlyProperties()`** makes properties or promoted parameters public and readonly:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\readonlyProperties())
  ```

  results in:

  ```php
  final class Foo
  {
      public function __construct(public readonly string $field)
      {
      }
  }
  ```

- **`privatizedConstructor()`** makes the class constructor private:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\privatizedConstructor())
  ```

  results in:

  ```php
  final class Foo
  {
      private function __construct(private string $field)
      {
      }
  }
  ```

- **`namedConstructor($name)`** creates a public static named constructor:

  ```php
  definitionOf(FooEvent::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\privatizedConstructor())
      ->with(Capabilities\namedConstructor('occurred'))
  ```

  results in:

  ```php
  final class FooEvent
  {
      private function __construct(private string $field)
      {
      }

      public static function occurred(string $field): self
      {
          return new self($field);
      }
  }
  ```

- **`getters()`** generates public getters for all fields:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\getters())
  ```

  results in:

  ```php
  final class Foo
  {
      public function __construct(private string $field)
      {
      }

      public function getField(): string
      {
          return $this->field;
      }
  }
  ```

- **`setters()`** generates public setters for all fields:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\setters())
  ```

  results in:

  ```php
  final class Foo
  {
      public function __construct(private string $field)
      {
      }

      public function setField(string $field): void
      {
          $this->field = $field;
      }
  }
  ```

- **`immutableSetters()`** generates public withers for all fields:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\constructorWithPromotedProperties())
      ->with(Capabilities\getters())
  ```

  results in:

  ```php
  final class Foo
  {
      public function __construct(private string $field)
      {
      }

      public function withField(string $field): self
      {
          $self = clone $this;
          $self->field = $field;
          return $self;
      }
  }
  ```

- **`implementedInterface()`** adds an `implements` clause to the generated class:

  ```php
  definitionOf(Foo::class)
      ->withField('field', 'string')
      ->with(Capabilities\implementedInterface(\IteratorAggregate::class))
  ```

  results in:

  ```php
  final class Foo implements IteratorAggregate
  {
  }
  ```

  > ⚠️ Please note that scaffolder DOES NOT check whether your class actually fulfills given interface. You can provide implementation using the `preservedAnnotatedMethods()` capability (see below).

### Adding and preserving logic

Scaffolder regenerates your classes every time it runs. If you make any changes to the generated classes, you will lose them the next time you run scaffolder. (Scaffolder prevents this by making the generated files read-only, but that can be easily worked around.) However, even DTOs can contain some simple logic, for example concatenating the first and last name.

Consider the following definition:

```php
return [
    definitionOf(Name::class, withFields: [
        'firstName' => 'string',
        'lastName' => 'string',
    ])
        ->with(Capabilities\constructorWithPromotedProperties())
        ->with(Capabilities\getters()),
];
```

It results in the generated class:

```php
<?php

/**
 * Do not edit. This is generated file. Modify definition file instead.
 */

declare(strict_types=1);

final class Name
{
    public function __construct(
        private string $firstName,
        private string $lastName,
    ) {
    }
    
    
    public function getFirstName(): string{
        return $this->firstName;
    }


    public function getLastName(): string{
        return $this->lastName;
    }
}
```

We want to add a `getFullName()` method and preserve it when scaffolder runs next time. The trick is to mark the method with the `#[KeepMethod]` attribute:

```php
#[\Grifart\ClassScaffolder\KeepMethod]
public function getFullName(): string
{
    return $this->firstName . ' ' . $this->lastName;
}
```

and add the `preservedAnnotatedMethods()` capability to the definition:

```php
$definition->with(Capabilities\preservedAnnotatedMethods())
```

The next time you run scaffolder, the `getFullName()` method will be kept intact as long as it has the `#[KeepMethod]` attribute.

Alternatively, you can use the `preservedMethod($methodName)` capability that keeps only methods that are explicitly listed in the capability function.

> ⚠️ The method-preserving capabilities are best accompanied by `preservedUseStatements()` capability which makes sure that all `use` statements from the original file are preserved.

### Custom capabilities

Capability is a very simple interface, so you can easily create and use your own:

```php
use Grifart\ClassScaffolder\Capabilities\Capability;
use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;

final class GetFullNameMethod implements Capability
{
    public function applyTo(
        ClassDefinition $definition, // lets you access the list of defined fields
        ClassInNamespace $draft,     // this is the prescription of the newly generated class
        ?ClassInNamespace $current,  // this describes the original class if it already exists
    ): void
    {
        $draft->getClassType()->addMethod('getFullName')
            ->setReturnType('string')
            ->addBody('return $this->firstName . " " . $this->lastName;');
    }
}
```

> ℹ️ Tip: If you need just single-purpuse capability, you can define it as a anonymous class. e.g.:
> 
> ```php
> ->with(new class implements Capability {
>   function applyTo() { /* the transformation */ }
> });
> ```


### Do not repeat yourself

As the definition file is a plain old PHP file, you can use any language construct to your advantage. We commonly define functions which preconfigure capabilities and even fields for repeating patterns:

```php
use Grifart\ClassScaffolder\Capabilities;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use function Grifart\ClassScaffolder\Definition\definitionOf;

function valueObject(string $className): ClassDefinition
{
    return definitionOf($className)
        ->with(Capabilities\constructorWithPromotedProperties())
        ->with(Capabilities\getters());
}
```

Such functions can then easily be reused throughout your definition files:

```php
return [
    $tag = valueObject(Tag::class)
        ->withField('name', 'string'),

    valueObject(Article::class)
        ->withField('id', 'int')
        ->withField('title', 'string')
        ->withField('content', 'string')
        ->withField('tags', listOf($tag))
        ->withField('archivedAt', nullable(\DateTime::class)),
];
```

> ⚠️ Scaffolder relies on Composer autoloader. To be able to access your functions in definition files, you should add them into the `files` autoloading section in `composer.json`, or wrap them into static classes that can be automatically autoloaded by Composer. If you have your  custom autoloader, please register this library as a command into your application. It will then use your custom environment.

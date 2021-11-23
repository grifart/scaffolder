<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\PhpLiteral;

final class ImmutableSetters implements Capability
{
	/** @var string[] */
	private array $fieldNames;

	/**
	 * @param string ...$fieldNames
	 */
	public function __construct(string ...$fieldNames)
	{
		$this->fieldNames = $fieldNames;
	}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$fieldsByName = [];
		foreach ($definition->getFields() as $field) {
			$fieldsByName[$field->getName()] = $field;
		}

		foreach ($this->fieldNames as $fieldName) {
			$field = $fieldsByName[$fieldName] ?? null;
			if ($field === null) {
				throw new \InvalidArgumentException(\sprintf(
					'Field %s not found in definition of class %s',
					$fieldName,
					$definition->getFullyQualifiedName(),
				));
			}

			$fieldType = $field->getType();

			$setter = $draft->getClassType()->addMethod(\sprintf('with%s', \ucfirst($fieldName)))
				->setVisibility('public')
				->setReturnType('self')
				->addBody('$self = clone $this;')
				->addBody('$self->? = ?;', [$fieldName, new PhpLiteral('$' . $fieldName)])
				->addBody('return $self;');

			$setter->addParameter($fieldName)
				->setType($fieldType->getTypeHint())
				->setNullable($fieldType->isNullable());

			if ($fieldType->requiresDocComment()) {
				$setter->addComment(\sprintf(
					'@param %s $%s',
					$fieldType->getDocCommentType($draft->getNamespace()),
					$fieldName,
				));
			}
		}
	}
}

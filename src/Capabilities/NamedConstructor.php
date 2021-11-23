<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use Nette\PhpGenerator\PhpLiteral;

final class NamedConstructor implements Capability
{
	public function __construct(
		private string $constructorName,
	) {}

	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$classType = $draft->getClassType();
		CapabilityTools::checkIfAllFieldsArePresent($definition, $classType);

		$namedCtor = $draft->getClassType()->addMethod($this->constructorName)
			->setReturnType('self')
			->setStatic();

		$fields = [];

		foreach ($definition->getFields() as $field) {
			$fieldName = $field->getName();
			$type = $field->getType();

			$fields[] = new PhpLiteral('$' . $fieldName);

			$parameter = $namedCtor->addParameter($fieldName);
			$parameter->setType($type->getTypeHint());
			$parameter->setNullable($type->isNullable());

			if ($type->requiresDocComment()) {
				$docCommentType = $type->getDocCommentType($draft->getNamespace());
				$namedCtor->addComment(\sprintf(
					'@param %s $%s',
					$docCommentType,
					$fieldName,
				));
			}
		}

		$namedCtor->addBody('return new self(...?);', [$fields]);
	}
}

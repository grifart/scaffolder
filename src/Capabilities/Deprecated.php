<?php declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Capabilities;

use Grifart\ClassScaffolder\ClassInNamespace;
use Grifart\ClassScaffolder\Definition\ClassDefinition;
use function sprintf;


final class Deprecated implements Capability
{

	/**
	 * @param class-string $replacement
	 */
	public function __construct(
		private string $replacement,
	) {}


	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$draft->getClassType()->addComment(sprintf(
			"@deprecated use @{%s} instead",
			$this->replacement,
		));
	}

}

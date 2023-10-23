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
		private ?string $description = null,
		private ?string $replacement = null,
	) {}


	public function applyTo(
		ClassDefinition $definition,
		ClassInNamespace $draft,
		?ClassInNamespace $current,
	): void
	{
		$annotation = '@deprecated';
		if ($this->description !== null) {
			$annotation .= ' ' . $this->description;
		}

		if ($this->replacement !== null) {
			$annotation .= $this->description !== null ? ', ' : ' ';
			$annotation .= sprintf('use {@see %s} instead', $this->replacement);
		}

		$draft->getClassType()->addComment($annotation);
	}

}

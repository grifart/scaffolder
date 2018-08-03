<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;


interface CompositeType extends Type
{

	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array;

}

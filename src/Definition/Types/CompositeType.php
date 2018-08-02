<?php

declare(strict_types = 1);

namespace Doklady\Scaffolder\Definition\Types;


interface CompositeType extends Type
{

	/**
	 * @return Type[]
	 */
	public function getSubTypes(): array;

}

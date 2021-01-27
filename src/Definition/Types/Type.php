<?php

declare(strict_types = 1);

namespace Grifart\ClassScaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;


interface Type
{

	public function getTypeHint(): string;


	public function isNullable(): bool;


	public function requiresDocComment(): bool;


	public function getDocCommentType(PhpNamespace $namespace): string;

}

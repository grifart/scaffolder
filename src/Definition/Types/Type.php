<?php

declare(strict_types = 1);

namespace Doklady\Scaffolder\Definition\Types;

use Nette\PhpGenerator\PhpNamespace;


interface Type
{

	public function getTypeHint(): string;


	public function isNullable(): bool;


	public function requiresDocComment(): bool;


	public function getDocCommentType(PhpNamespace $namespace): string;


	public function hasComment(): bool;


	public function getComment(PhpNamespace $namespace): ?string;

}

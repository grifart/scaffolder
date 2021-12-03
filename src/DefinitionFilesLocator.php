<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder;

use Nette\Utils\Finder;

final class DefinitionFilesLocator
{
	/**
	 * @return DefinitionFile[]
	 */
	public function locateDefinitionFiles(
		string $path,
		string $searchPattern,
	): array
	{
		$result = [];

		if (\is_dir($path)) {
			$files = Finder::findFiles($searchPattern)->from($path);
			foreach ($files as $file) {
				$result[] = DefinitionFile::from($file->getPathname());
			}
		} elseif (\is_file($path)) {
			$result[] = DefinitionFile::from($path);
		} else {
			throw new \RuntimeException('Given path is neither a file nor a directory.');
		}

		return $result;
	}
}

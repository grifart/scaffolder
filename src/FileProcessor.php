<?php

declare(strict_types=1);

namespace Grifart\ClassScaffolder;

final class FileProcessor
{
	/**
	 * @param (\Closure(Definition\ClassDefinition): DefinitionResult) $processDefinition
	 */
	public function processFile(
		DefinitionFile $definitionFile,
		\Closure $processDefinition,
	): FileResult
	{
		try {
			$definitions = $definitionFile->load();
		} catch (\Throwable $error) {
			return new FileResult($definitionFile, $error);
		}

		$result = new FileResult($definitionFile, null);
		foreach ($definitions as $definition) {
			try {
				$definitionResult = $processDefinition($definition);
				$result->addDefinition($definitionResult);
			} catch (\Throwable $error) {
				$result->addDefinition(DefinitionResult::error($definition, $error));
			}
		}

		return $result;
	}
}

namespace NS;

final class CLS
{
	/**
	 * @param string[] $field
	 */
	private function __construct(
		private array $field,
		private string $anotherField,
	) {
	}


	/**
	 * @param string[] $field
	 */
	public static function of(array $field, string $anotherField): self
	{
		return new self($field, $anotherField);
	}
}

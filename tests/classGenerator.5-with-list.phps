namespace NS;

final class CLS
{
	/** @var array<int, string> */
	private array $field;


	/**
	 * @param array<int, string> $field
	 */
	public function __construct(array $field)
	{
		$this->field = $field;
	}


	/**
	 * @return array<int, string>
	 */
	public function getField(): array
	{
		return $this->field;
	}
}

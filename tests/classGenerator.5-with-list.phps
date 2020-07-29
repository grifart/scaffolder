namespace NS;

final class CLS
{
	/** @var array|string[] [int => string] */
	private array $field;


	/**
	 * @param array|string[] $field [int => string]
	 */
	public function __construct(array $field)
	{
		$this->field = $field;
	}


	/**
	 * @return array|string[] [int => string]
	 */
	public function getField(): array
	{
		return $this->field;
	}
}

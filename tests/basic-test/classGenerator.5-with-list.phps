namespace NS;

final class CLS
{
	/** @var string[] */
	private array $field;


	/**
	 * @param string[] $field
	 */
	public function __construct(array $field)
	{
		$this->field = $field;
	}


	/**
	 * @return string[]
	 */
	public function getField(): array
	{
		return $this->field;
	}
}

namespace NS;

final class CLS
{
	/** @var string */
	private $field;


	public function __construct(string $field)
	{
		$this->field = $field;
	}


	public function getField(): string
	{
		return $this->field;
	}
}
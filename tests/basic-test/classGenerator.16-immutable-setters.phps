namespace NS;

final class CLS
{
	/**
	 * @param string[] $field
	 */
	public function __construct(private array $field)
	{
	}


	/**
	 * @param string[] $field
	 */
	public function withField(array $field): self
	{
		$self = clone $this;
		$self->field = $field;
		return $self;
	}
}

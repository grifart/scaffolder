namespace NS;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use SplFixedArray;
use SplObjectStorage;

final class CLS
{
	/** @var SplObjectStorage|SplFixedArray[] [ClassDefinition => SplFixedArray] */
	private $field;


	/**
	 * @param SplObjectStorage|SplFixedArray[] $field [ClassDefinition => SplFixedArray]
	 */
	public function __construct(SplObjectStorage $field)
	{
		$this->field = $field;
	}


	/**
	 * @return SplObjectStorage|SplFixedArray[] [ClassDefinition => SplFixedArray]
	 */
	public function getField(): SplObjectStorage
	{
		return $this->field;
	}
}
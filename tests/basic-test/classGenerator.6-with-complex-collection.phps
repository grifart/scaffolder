namespace NS;

use Grifart\ClassScaffolder\Definition\ClassDefinition;
use SplFixedArray;
use SplObjectStorage;

final class CLS
{
	/** @var SplObjectStorage<ClassDefinition, SplFixedArray> */
	private SplObjectStorage $field;


	/**
	 * @param SplObjectStorage<ClassDefinition, SplFixedArray> $field
	 */
	public function __construct(SplObjectStorage $field)
	{
		$this->field = $field;
	}


	/**
	 * @return SplObjectStorage<ClassDefinition, SplFixedArray>
	 */
	public function getField(): SplObjectStorage
	{
		return $this->field;
	}
}

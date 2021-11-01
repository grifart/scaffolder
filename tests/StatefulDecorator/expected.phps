namespace Grifart\ClassScaffolder\Test;

use Grifart\Stateful\State;
use Grifart\Stateful\StateBuilder;
use Grifart\Stateful\Stateful;

final class StatefulDecorator implements Stateful
{
	public function __construct(private string $field)
	{
	}


	public function _getState(): State
	{
		return StateBuilder::from($this)
			->version(1)
			->field('field', $this->field)
			->build();
	}


	public static function _fromState(State $state): static
	{
		$state->ensureVersion(1);
		$self = $state->makeAnEmptyObject(self::class);
		\assert($self instanceof static);

		/** @var array{field: string} $state */
		$self->field = $state['field'];

		return $self;
	}
}

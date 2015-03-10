<?php

	namespace Corelib\Data\Model
	{
		use Corelib\Data\Hydratable;
		use Corelib\Data\Hydrator;

		abstract class AbstractModel
		implements Hydratable
		{
			public function __construct($values = null, $map_id = null, $hints_only = false)
			{
				Hydrator::sharedHydrator()->fromArray($this, $values, $map_id, $hints_only);
			}

			public function toArray($map_id = null, $hints_only = false)
			{
				return (Hydrator::sharedHydrator()->toArray($this, $map_id, $hints_only));
			}

			public function __fromArray($map_id = null)
			{
				return ([]);
			}

			public function __toArray($map_id = null)
			{
				return ([]);
			}
		}
	}
<?php

	namespace Corelib\Data
	{
		interface Hydratable
		{
			public function __fromArray($map_id = null);
			public function __toArray($map_id = null);
		}
	}
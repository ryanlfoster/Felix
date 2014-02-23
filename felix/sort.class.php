<?php

/**
 * Class Sort
 */

class Sort {
	public $field = null;
	public $type = null;

	public function __construct(&$field = array('date'), &$type = 'date') {
		if(is_string($field)) $field = array($field);

		// Save this data for later
		$this->field = $field;
		$this->type = $type;
	}

	public function sort($a, $b) {
		// Let's not waste any time
		if($this->type === 'random') return mt_rand(-1, 1);

		$field = $this->field;

		// Iterate so we can compare the right field
		while(count($field) > 0) {
			$k = array_shift($field);

			// If specified field exists on both parties,
			// replace old values by new ones
			if(isset($a->$k) && isset($b->$k)) {
				$a = $a->$k;
				$b = $b->$k;
			} else {
				return 0;
			}
		}

		switch($this->type) {
			case 'date':
				$a = strtotime($a);
				$b = strtotime($b);
				break;
			case 'number':
				$a = floatval($a);
				$b = floatval($b);
				break;
			case 'string':
				return strcmp($a, $b);
		}

		return $b - $a;
	}
}

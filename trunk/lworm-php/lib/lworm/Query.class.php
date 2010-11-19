<?php

	interface Query {
	
		const FILTER_OP_EQUAL = 0;
		const FILTER_OP_GT = 1;
		const FILTER_OP_GT_EQUAL = 2;
		const FILTER_OP_LT = 3;
		const FILTER_OP_LT_EQUAL = 4;
		const FILTER_OP_NOT_EQUAL = 5;
		
		const SORT_ASC = 6;
		const SORT_DESC = 7;
	
		public function getEntities($limit = 0);
		public function addFilter($field, $op, $value);
		public function addSort($field, $direction);
	}
	
?>
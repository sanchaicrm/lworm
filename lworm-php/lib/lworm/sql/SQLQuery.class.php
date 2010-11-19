<?php
	require_once "lib/lworm/Query.class.php";

	class SQLQuery implements Query {
		
		public function __construct($database_adapter, $entity_mapper, $entity_class) {
			$this->database_adapter = $database_adapter;
			$this->entity_mapper = $entity_mapper;
			$this->entity_class = $entity_class;
			$this->filters = "";
			$this->sort = "";
		}
	
		public function getEntities($limit = 0) {
			$query = $this->generateQuery($limit);
			$rows = $this->database_adapter->executeQuery($query);
			$entities = array();
			foreach($rows as $row) {
				$entities[] = $this->entity_mapper->generateEntity($this->entity_class, $row);
			}
			return $entities;
		}
		
		private function generateQuery($limit) {
			$entity_class = $this->entity_class;
			$query = "SELECT * FROM " . $entity_class::_getType();
			if($this->filters)
				$query .= " WHERE " . substr($this->filters, 4);
			if($this->sort)
				$query .= " ORDER BY " . substr($this->sort, 2);
			if($limit)
				$query .= " LIMIT " . $limit;
			return $query;
		}
				
		public function addFilter($field, $op, $value) {
			$this->filters .= "AND `" . $field . "` " . $this->generateOp($op) . " '" . mysql_real_escape_string($value) . "' "; 
			return $this;
		}
		
		private function generateOp($op) {
			if($op == Query::FILTER_OP_EQUAL)
				return "=";
			if($op == Query::FILTER_OP_GT)
				return ">";
			if($op == Query::FILTER_OP_GT_EQUAL)
				return ">=";
			if($op == Query::FILTER_OP_LT)
				return "<";
			if($op == Query::FILTER_OP_LT_EQUAL)
				return "<=";
			if($op == Query::FILTER_OP_NOT_EQUAL)
				return "!=";
		}
		
		public function addSort($field, $direction) {
			$this->sort .= ", " . $field . " " . ($direction == Query::SORT_DESC ? "DESC" : "ASC");
			return $this;
		}

	}
	
?>
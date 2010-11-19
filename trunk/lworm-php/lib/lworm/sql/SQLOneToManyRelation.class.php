<?php
	require_once "lib/lworm/OneToManyRelation.class.php";

	class SQLOneToManyRelation implements OneToManyRelation {
		
		public function __construct($database_adapter, $entity_mapper, $src_entity, $target_entity, $relation_field, $id) {
			$this->database_adapter = $database_adapter;
			$this->entity_mapper = $entity_mapper;						
			$this->src_entity = $src_entity;
			$this->target_entity = $target_entity;
			$this->relation_field = $relation_field;
			$this->entity_id = $id;
		}
		
		public function getEntities() {
			$query = "SELECT t.* FROM `" . $this->target_entity . "` t WHERE t." . $this->relation_field . " = '" . $this->entity_id . "'";
			$rows = $this->database_adapter->executeQuery($query);
			$entities = array();
			foreach($rows as $row) {
				$entities[] = $this->entity_mapper->generateEntity($this->target_entity, $row);
			}
			return $entities;
		}
	
	}

?>
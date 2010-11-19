<?php
	require_once "lib/lworm/ManyToOneRelation.class.php";

	class SQLManyToOneRelation implements ManyToOneRelation {
	
		public function __construct($database_adapter, $entity_mapper, $src_entity, $target_entity, $relation_field, $id) {
			$this->database_adapter = $database_adapter;
			$this->entity_mapper = $entity_mapper;						
			$this->src_entity = $src_entity;
			$this->target_entity = $target_entity;
			$this->relation_field = $relation_field;
			$this->entity_id = $id;
		}
	
		public function setEntity($entity) {
			$query = "UPDATE `" . $this->src_entity . "` " .
			"SET `" . $this->relation_field . "` = '" . $entity->getId() . "' " .
			"WHERE id='" . $this->entity_id . "'";
			$this->database_adapter->execute($query);
		}
		
		public function getEntity() {
			$query = "SELECT t.* FROM `" . $this->src_entity . "` s, `" . $this->target_entity . "` t " .
			"WHERE t.id = s." . $this->relation_field . " AND s.id='" . $this->entity_id . "'";
			$rows = $this->database_adapter->executeQuery($query);
			return $this->entity_mapper->generateEntity($this->target_entity, $rows[0]);
		}
		
	}
	
?>
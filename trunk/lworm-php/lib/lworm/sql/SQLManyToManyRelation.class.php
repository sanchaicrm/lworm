<?php
	require_once "lib/lworm/ManyToManyRelation.class.php";

	class SQLManyToManyRelation implements ManyToManyRelation {
		
		public function __construct($database_adapter, $entity_mapper, $src_entity, $target_entity, $relation_entity, $id) {
			$this->database_adapter = $database_adapter;
			$this->entity_mapper = $entity_mapper;			
			$this->src_entity = $src_entity;
			$this->target_entity = $target_entity;
			$this->relation_entity = $relation_entity;
			$this->entity_id = $id;
		}
		
		public function addEntity($entity) {
			$query = "INSERT INTO " . $this->relation_entity . 
			" (" . 
			" `" . $this->src_entity . "_id`, " .
			" `" . $this->target_entity . "_id`" .
			") VALUES ('%s', '%s')";
			$query = vsprintf($query, array($this->entity_id, $entity->getId()));
			$this->database_adapter->execute($query);
		}
		
		public function removeEntity($entity) {
			$query = "DELETE FROM " . $this->relation_entity . " WHERE " .
			$this->src_entity . "_id = '" . $this->entity_id . "' AND " .
			$this->target_entity . "_id = '" . $entity->getId() . "'";
			$this->database_adapter->execute($query);
		}
		
		public function getEntities() {
			$query = "SELECT t.* FROM `" . $this->relation_entity . "` r, `" . $this->target_entity . "` t " .
			"WHERE t.id = r." . $this->target_entity . "_id AND r." . $this->src_entity . "_id='" . $this->entity_id . "'";
			$rows = $this->database_adapter->executeQuery($query);
			$entities = array();
			foreach($rows as $row) {
				$entities[] = $this->entity_mapper->generateEntity($this->target_entity, $row);
			}
			return $entities;
		}

	}
?>
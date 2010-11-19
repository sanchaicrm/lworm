<?php
	require_once "lib/lworm/DataStore.class.php";
	require_once "lib/lworm/sql/SQLQuery.class.php";
	require_once "lib/lworm/sql/SQLManyToManyRelation.class.php";
	require_once "lib/lworm/sql/SQLManyToOneRelation.class.php";
	require_once "lib/lworm/sql/SQLOneToManyRelation.class.php";
	
	class SQLDataStore implements DataStore {
		
		public function __construct($database_adapter, $entity_mapper) {
			$this->database_adapter = $database_adapter;
			$this->entity_mapper = $entity_mapper;
		}
		
		public function createSchema($yaml_file) {
			$schema = spyc_load_file($yaml_file);
			foreach($schema as $entity_name => $entity_definition) {
				$this->createTable($entity_name, $entity_definition);
				if($entity_definition['relations']['many-to-many'])
					$this->createManyToManyRelations($entity_definition['relations']['many-to-many'], $entity_name);
			}
		}
		
		private function createManyToManyRelations($definition, $entity_name) {
			foreach($definition as $relation_name => $relation_type) {
				$pos = strpos($relation_type, '(');
				$relation_entity = substr($relation_type, $pos+1, strlen($relation_type)-$pos-2);
				$relation_type = substr($relation_type, 0, $pos);
				$query = "";
				$query .= "CREATE TABLE IF NOT EXISTS `" . $relation_entity . "` (";
				$query .= "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
				$query .= $entity_name . "_id INT, ";
				$query .= $relation_type . "_id INT";				
				$query .= ")";
				$this->database_adapter->execute($query);			
			}
		}
		
		private function createTable($entity_name, $entity_definition) {
			$query = "";
			$query .= "CREATE TABLE IF NOT EXISTS `" . $entity_name . "` (";
			$query .= "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
			$fields = $entity_definition['fields'];
			foreach($fields as $field_name => $field_type) {
				$query .= ", `" . $field_name . "` " . $this->entity_mapper->convertFieldType($field_type); 
			}
			$many_to_one = $entity_definition['relations']['many-to-one'];
			if($many_to_one) {
				foreach($many_to_one as $field_name => $field_type) {
					$query .= ", `" . $field_name . "_id` INT"; 
				}			
			}
			$query .= ")";
			$this->database_adapter->execute($query);
		}
				
		public function save($entity) {
			if($entity->getId())
				$this->updateEntity($entity);
			else
				$this->insertEntity($entity);
		}

		private function updateEntity($entity) {
			$query = "";
			$query .= "UPDATE `" . $entity::_getType() . "` SET ";
			$fields = "";
			foreach($entity::_getFieldNames() as $field_name) {
				if($field_name == 'id')
					continue;
				$fields .= ",`" . $field_name . "` = '%s'";
			}
			$query .= substr($fields, 1);
			$query .= " WHERE id='" . $entity->getId() . "'";
			$query = $this->entity_mapper->setupQueryFromEntity($query, $entity);
			$this->database_adapter->execute($query);
		}

		private function insertEntity($entity) {
			$query = "";
			$query = "INSERT INTO `" . $entity::_getType() . "` (";
			$field_names = "";
			$fields = "";
			foreach($entity::_getFieldNames() as $field_name) {
				if($field_name == 'id')
					continue;
				$field_names .= ",`" . $field_name . "`";
				$fields .= ",'%s'";
			}
			$query .= substr($field_names, 1);
			$query .= ") VALUES (";
			$query .= substr($fields, 1);
			$query .= ")";
			$query = $this->entity_mapper->setupQueryFromEntity($query, $entity);
			$this->database_adapter->execute($query);
			$result = $this->database_adapter->executeQuery("SELECT MAX(id) as max_id FROM `" . $entity::_getType() . "`");
			$entity->setId($result[0]['max_id']);
		}
				
		public function remove($entity) {
			$this->database_adapter->execute("DELETE FROM " . $entity::_getType() . " WHERE id='" . $entity->getId() . "'", $this->connection);
		}
		
		public function getEntity($entity_class, $id) {
			$entities = $this->createQuery($entity_class)->addFilter('id', Query::FILTER_OP_EQUAL, $id)->getEntities();
			return $entities[0];
		}

		
		public function createQuery($entity_class) {
			return new SQLQuery($this->database_adapter, $this->entity_mapper, $entity_class);
		}

		public function getManyToManyRelation($src_entity, $target_entity, $relation_entity, $id) {
			return new SQLManyToManyRelation($this->database_adapter, $this->entity_mapper, $src_entity, $target_entity, $relation_entity, $id);
		}
		
		public function getManyToOneRelation($src_entity, $target_entity, $relation_field, $id) {
			return new SQLManyToOneRelation($this->database_adapter, $this->entity_mapper, $src_entity, $target_entity, $relation_field, $id);
		}
		
		public function getOneToManyRelation($src_entity, $target_entity, $relation_field, $id) {
			return new SQLOneToManyRelation($this->database_adapter, $this->entity_mapper, $src_entity, $target_entity, $relation_field, $id);
		}

		public function execute($query) {
			$this->database_adapter->execute($query);
		}
	}
	
?>
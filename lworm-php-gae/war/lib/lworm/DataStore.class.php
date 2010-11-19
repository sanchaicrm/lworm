<?php

	interface DataStore {
		public function save($entity);
		public function remove($entity);
		public function getEntity($entity_class, $id);
		public function createQuery($entity_class);
		public function getManyToManyRelation($src_entity, $target_entity, $relation_entity, $id);
		public function getManyToOneRelation($src_entity, $target_entity, $relation_field, $id);
		public function getOneToManyRelation($src_entity, $target_entity, $relation_field, $id);
	}
	
?>
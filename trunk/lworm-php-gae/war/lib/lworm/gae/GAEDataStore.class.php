<?php
	import com.estontorise.gae_datastore.NonCachedDataStore;
	import com.google.appengine.api.datastore.Entity;
	import com.google.appengine.api.datastore.KeyFactory;
	import java.util.UUID;

	require_once "lib/lworm/DataStore.class.php";
	require_once "lib/lworm/gae/GAEQuery.class.php";
	require_once "lib/lworm/gae/GAEManyToOneRelation.class.php";
	require_once "lib/lworm/gae/GAEOneToManyRelation.class.php";
	require_once "lib/lworm/gae/GAEManyToManyRelation.class.php";
	
	class GAEDataStore implements DataStore {
		
		private function generateKey(&$entity) {
			if(!$entity->getId()) {
				$uuid = UUID::randomUUID()->toString();
				$entity->setId($uuid);
			}
			return KeyFactory::createKey("lworm_" . $entity->_getType(), $entity->getId());
		}
		
		public function save(&$entity) {
			$key = $this->generateKey($entity);
			
			$ds_entity = new Entity($key);
			$entity_fields = $entity->_getFields();
			foreach($entity->_getFieldNames() as $field_name) {
				if($field_name == 'id')
					continue;
				$ds_entity->setProperty($field_name, $entity_fields[$field_name]);
			}
			NonCachedDataStore::put($ds_entity);
		}
		
		public function remove($entity) {
			$key = $this->generateKey($entity);

			NonCachedDataStore::remove($key);
		}

		public function convertEntity($ds_entity, $entity_class) {
			$entity = new $entity_class;
			foreach($entity->_getFieldNames() as $field_name) {
				$entity->fields[$field_name] = $ds_entity->getProperty($field_name);
			}
			$entity->setId($ds_entity->getKey()->getName());
			return $entity;
		}
		
		public function getEntity($entity_class, $id) {
			$key = KeyFactory::createKey("lworm_" . $entity_class, $id);
			$ds_entity = NonCachedDataStore::get($key);

			if($ds_entity) 
				return $this->convertEntity($ds_entity, $entity_class);
			
			return FALSE;
		}
		
		public function createQuery($entity_class) {
			return new GAEQuery($this, $entity_class);
		}
		
		public function getManyToManyRelation($src_entity, $target_entity, $relation_entity, $id) {
			return new GAEManyToManyRelation($this, $src_entity, $target_entity, $relation_entity, $id);
		}
		
		public function getManyToOneRelation($src_entity, $target_entity, $relation_field, $id) {
			return new GAEManyToOneRelation($this, $src_entity, $target_entity, $relation_field, $id);
		}
		
		public function getOneToManyRelation($src_entity, $target_entity, $relation_field, $id) {
			return new GAEOneToManyRelation($this, $src_entity, $target_entity, $relation_field, $id);
		}
	}
?>
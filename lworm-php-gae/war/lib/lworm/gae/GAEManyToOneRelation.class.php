<?php
	import com.estontorise.gae_datastore.CachedDataStore;

	require_once "lib/lworm/ManyToOneRelation.class.php";

	class GAEManyToOneRelation implements ManyToOneRelation {
		
		public function __construct($ds, $src_entity, $target_entity, $relation_field, $id) {
			$this->ds = $ds;
			$this->target_entity = $target_entity;
			$this->relation_field = $relation_field;
			$this->src_key = KeyFactory::createKey("lworm_" . $src_entity, $id);
		}
				
		public function setEntity($entity) {
			$ds_entity = CachedDataStore::get($this->src_key);
			$ds_entity->setProperty($this->relation_field, $entity->getId());
			CachedDataStore::put($ds_entity);
		}
		
		public function getEntity() {
			$ds_entity = CachedDataStore::get($this->src_key);
			$target_id = $ds_entity->getProperty($this->relation_field);
			return $this->ds->getEntity($this->target_entity, $target_id);
		}
		
	}

?>
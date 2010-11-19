<?php
	import com.estontorise.gae_datastore.NonCachedDataStore;
	import com.google.appengine.api.datastore.Entity;
	import com.google.appengine.api.datastore.KeyFactory;
	import java.util.UUID;
	
	require_once "lib/lworm/ManyToManyRelation.class.php";

	class GAEManyToManyRelation implements ManyToManyRelation {
		
		public function __construct($ds, $src_entity, $target_entity, $relation_entity, $id) {
			$this->ds = $ds;
			$this->src_entity = $src_entity;
			$this->target_entity = $target_entity;
			$this->relation_entity = $relation_entity;
			$this->id = $id;
		}
				
		public function addEntity($entity) {
			$uuid = UUID::randomUUID()->toString();
			$key = KeyFactory::createKey("lworm_" . $this->relation_entity, $uuid);
			$ds_entity = new Entity($key);
			$ds_entity->setProperty($this->src_entity . "_id", $this->id);
			$ds_entity->setProperty($this->target_entity . "_id", $entity->getId());
			NonCachedDataStore::put($ds_entity);
		}
		
		public function removeEntity($entity) {
			$query = $this->ds->createQuery($this->relation_entity);
			$query->addFilter($this->src_entity . "_id", Query::FILTER_OP_EQUAL, $this->id);
			$query->addFilter($this->target_entity . "_id", Query::FILTER_OP_EQUAL, $entity->getId());
			$entity_iterator = $query->getEntityIterator();
			while($entity_iterator->hasNext()) {
				$ds_entity = $entity_iterator->next(); 
				NonCachedDataStore::remove($ds_entity->getKey());
			}
		}
		
		public function getEntities() {
			$query = $this->ds->createQuery($this->relation_entity);	
			$query->addFilter($this->src_entity . "_id", Query::FILTER_OP_EQUAL, $this->id);
			$entities = array();
			$entity_iterator = $query->getEntityIterator();
			while($entity_iterator->hasNext()) {
				$ds_entity = $entity_iterator->next();
				$target_id = $ds_entity->getProperty($this->target_entity . "_id");
				$entities[] = $this->ds->getEntity($this->target_entity, $target_id);
			}
			return $entities;
		}
		
	}

?>
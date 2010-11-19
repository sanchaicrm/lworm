<?php
	import com.estontorise.lworm_php.gae.GAEQueryAdapter;

	require_once "lib/lworm/Query.class.php";

	class GAEQuery implements Query {
		
		public function __construct($ds, $entity_class) {
			$this->ds = $ds;
			$this->entity_class = $entity_class;
			$this->query = new GAEQueryAdapter($entity_class);
		}
		
		public function getEntityIterator() {
			return $this->query->getEntityIterator();
		}
		
		public function getEntities($limit = 0) {
			$entity_iterator = $this->query->getEntityIterator();
			$entities = array();
			$cnt = 0;
			while($entity_iterator->hasNext()) {
				$ds_entity = $entity_iterator->next();
				$cnt++;
				if($limit > 0 && $cnt > $limit)
					return $entities;
				$entities[] = $this->ds->convertEntity($ds_entity, $this->entity_class);
			}
			return $entities;
		}
		
		public function addFilter($field, $op, $value) {
			$this->query = $this->query->addFilter($field, $op, $value);
			return $this;
		}
		
		public function addSort($field, $direction) {
			$this->query = $this->query->addSort($field, $direction);
			return $this;
		}
		
	}

?>
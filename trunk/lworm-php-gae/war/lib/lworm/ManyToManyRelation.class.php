<?php

	interface ManyToManyRelation {
		public function addEntity($entity);
		public function removeEntity($entity);
		public function getEntities();
	}
	
?>
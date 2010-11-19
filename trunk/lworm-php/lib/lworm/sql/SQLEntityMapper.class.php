<?php
	
	interface SQLEntityMapper {
		public function convertFieldType($field_type);
		public function setupQueryFromEntity($query, $entity);
		public function generateEntity($entity_class, $row);
	}
	
?>
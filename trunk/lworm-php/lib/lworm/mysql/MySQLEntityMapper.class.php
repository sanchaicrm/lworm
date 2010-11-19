<?php
	require_once "lib/lworm/sql/SQLEntityMapper.class.php";

	class MySQLEntityMapper implements SQLEntityMapper {
	
		public function convertFieldType($field_type) {
			if($field_type == 'text')
				return "TEXT";
			if($field_type == 'boolean')
				return "CHAR(1)";
		}
		
		public function setupQueryFromEntity($query, $entity) {
			$field_values = array();
			$entity_fields = $entity->_getFields();
			foreach($entity::_getFieldNames() as $field_name) {
				if($field_name == 'id')
					continue;
				$field_values[] = mysql_real_escape_string($entity_fields[$field_name]);
			}
			$query = vsprintf($query, $field_values);
			return $query;
		}

		public function generateEntity($entity_class, $row) {
			$entity = new $entity_class;
			foreach($entity::_getFieldNames() as $field_name) {
				$entity->fields[$field_name] = $row[$field_name];
			}
			return $entity;		
		}
	
	}
	
?>
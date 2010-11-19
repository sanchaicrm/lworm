<?php
	require_once "lib/spyc/spyc.php";
	
	class ModelGenerator {
				
		public function generateModel($yaml_file, $dir) {
			$schema = spyc_load(file_get_contents($yaml_file));
			$this->dir = $dir;
			foreach($schema as $entity_name => $entity_definition) {
				$this->generateClass($dir . '/' . $entity_name . '.class.php', $entity_name, $entity_definition);
			}
		}
		
		private function generateClass($class_file, $class_name, $definition) {
			$constructor = "";
			$fields = "";
			$field_names = "";
		
			$this->generateFields($definition['fields'], $constructor, $fields, $field_names);
			$this->generateRelations($definition['relations'], $class_name, $constructor, $fields, $field_names);
			
			$content = "";
			$content .= "<?php\n\n";
			$content .= "class " . $class_name . " {\n\n";
			$content .= "\tpublic function __construct() {\n";
			$content .= "\t\t\$this->fields = array();\n";
			$content .= $constructor;
			$content .= "\t}\n\n";

			$content .= "\tpublic function _getType() {\n";
			$content .= "\t\treturn \"" . $class_name . "\";\n";
			$content .= "\t}\n\n";

			$content .= "\tpublic function _getFieldNames() {\n";
			$content .= "\t\treturn array(" . $field_names . ");\n";
			$content .= "\t}\n\n";

			$content .= "\tpublic function _getFields() {\n";
			$content .= "\t\treturn \$this->fields;\n";
			$content .= "\t}\n\n";


			$content .= $fields;
			$content .= "}\n\n";
			$content .= "?>";
			file_put_contents($class_file, $content);
		}
		
		private function generateFields($fields, &$constructor, &$entity_fields, &$field_names) {
			$content = "";
			$content .= "\tpublic function setId(\$id) {\n";
			$content .= "\t\t\$this->fields['id'] = \$id;\n";
			$content .= "\t}\n\n";
			$content .= "\tpublic function getId() {\n";
			$content .= "\t\treturn \$this->fields['id'];\n";
			$content .= "\t}\n\n";
			$field_names = "'id'";
			foreach($fields as $field_name => $field_type) {
				$content .= "\tpublic function set" . ucfirst($field_name) . "(\$" . $field_name . ") {\n";
				$content .= "\t\t\$this->fields['" . $field_name . "'] = \$" . $field_name . ";\n";
				$content .= "\t}\n\n";
				$content .= "\tpublic function " . ($field_type == 'boolean' ? 'is' : 'get') . ucfirst($field_name) . "() {\n";
				$content .= "\t\treturn \$this->fields['" . $field_name . "'];\n";
				$content .= "\t}\n\n";
				$field_names .= ", '" . $field_name . "'";
			}
			$entity_fields .= $content;
		}
		
		private function generateRelations($relations, $class_name, &$constructor, &$fields, &$field_names) {
			if($relations['many-to-many'])
				$this->generateRelationType($relations['many-to-many'], $class_name, "ManyToMany", $constructor, $fields, $field_names);
			if($relations['many-to-one'])
				$this->generateRelationType($relations['many-to-one'], $class_name, "ManyToOne", $constructor, $fields, $field_names);
			if($relations['one-to-many'])
				$this->generateRelationType($relations['one-to-many'], $class_name, "OneToMany", $constructor, $fields, $field_names);
		}
		
		private function generateRelationType($relations, $class_name, $relation_class, &$constructor, &$fields, &$field_names) {
			$content = "";
			foreach($relations as $relation_name => $relation_type) {
				$content .= "\tpublic function get" . ucfirst($relation_name) . "Relation(\$ds) {\n";
				$pos = strpos($relation_type, '(');
				if($pos) {
					$relation_entity = substr($relation_type, $pos+1, strlen($relation_type)-$pos-2);
					$relation_type = substr($relation_type, 0, $pos);
					if($relation_class == "ManyToMany") {
						$this->generateRelationEntity($relation_entity, $class_name, $relation_type);
						$content .= "\t\treturn \$ds->get" . $relation_class . "Relation('" . $class_name . "', '" . $relation_type . "', '" . $relation_entity . "', \$this->getId());\n";
					}
					if($relation_class == "OneToMany") {
						$content .= "\t\treturn \$ds->get" . $relation_class . "Relation('" . $class_name . "', '" . $relation_type . "', '" . $relation_entity . "_id', \$this->getId());\n";					
					}
				} else
					$content .= "\t\treturn \$ds->get" . $relation_class . "Relation('" . $class_name . "', '" . $relation_type . "', '" . $relation_name . "_id', \$this->getId());\n";
				$content .= "\t}\n\n";
			}
			$fields .= $content;
		}
		
		private function generateRelationEntity($entity_name, $class_name, $relation_type) {
			$entity_definition = array(
				'fields' => array(
					$class_name . '_id' => 'key',
					$relation_type . '_id' => 'key'
				)
			);
			$this->generateClass($this->dir . '/' . $entity_name . '.class.php', $entity_name, $entity_definition);
		}
		
	}
	
?>
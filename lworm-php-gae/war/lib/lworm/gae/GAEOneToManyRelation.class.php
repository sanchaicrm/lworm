<?php
require_once "lib/lworm/OneToManyRelation.class.php";

class GAEOneToManyRelation implements OneToManyRelation {

	public function __construct($ds, $src_entity, $target_entity, $relation_field, $id) {
		$this->ds = $ds;
		$this->id = $id;
		$this->target_entity = $target_entity;
		$this->relation_field = $relation_field;
	}

	public function getEntities() {
		return $this->ds->createQuery($this->target_entity)->addFilter($this->relation_field, Query::FILTER_OP_EQUAL, $this->id)->getEntities();
	}

}

?>
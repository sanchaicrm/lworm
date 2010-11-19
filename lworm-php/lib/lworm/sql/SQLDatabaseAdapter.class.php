<?php

	interface SQLDatabaseAdapter {
		public function execute($query);
		public function executeQuery($query, $limit = 0);
	}
	
?>
<?php
	require_once "lib/lworm/sql/SQLDatabaseAdapter.class.php";

	class MySQLDatabaseAdapter implements SQLDatabaseAdapter {
		
		public function __construct($db_host, $db_name, $db_user, $db_password) {
			$this->connection = mysql_connect($db_host, $db_user, $db_password);
			mysql_select_db($db_name, $this->connection);
		}

		public function execute($query) {
			mysql_query($query, $this->connection);
		}
		
		public function executeQuery($query, $limit = 0) {
			$rows = array();
			$result = mysql_query($query, $this->connection);
			while($row = mysql_fetch_assoc($result)) {
				$rows[] = $row;
			}
			mysql_free_result($result);
			return $rows;
		}

	}
	
?>
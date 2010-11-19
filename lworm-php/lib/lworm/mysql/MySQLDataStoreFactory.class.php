<?php
	require_once "lib/lworm/sql/SQLDataStore.class.php";
	require_once "lib/lworm/mysql/MySQLDatabaseAdapter.class.php";
	require_once "lib/lworm/mysql/MySQLEntityMapper.class.php";

	class MySQLDataStoreFactory {
		
		public static function getDataStore($db_host, $db_name, $db_user, $db_password) {
			return new SQLDataStore(new MySQLDatabaseAdapter($db_host, $db_name, $db_user, $db_password), new MySQLEntityMapper);
		}
		
	}
?>
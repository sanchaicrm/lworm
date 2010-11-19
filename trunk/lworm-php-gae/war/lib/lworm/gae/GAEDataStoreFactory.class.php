<?php
	require_once "lib/lworm/gae/GAEDataStore.class.php";

	class GAEDataStoreFactory {
		
		public static function getDataStore() {
			return new GAEDataStore;	
		}
		
	}
?>
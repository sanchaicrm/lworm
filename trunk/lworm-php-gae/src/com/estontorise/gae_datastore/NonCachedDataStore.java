package com.estontorise.gae_datastore;

import com.google.appengine.api.datastore.DatastoreService;
import com.google.appengine.api.datastore.DatastoreServiceFactory;
import com.google.appengine.api.datastore.Entity;
import com.google.appengine.api.datastore.EntityNotFoundException;
import com.google.appengine.api.datastore.Key;

public class NonCachedDataStore {

	public static Entity get(Key key) {
		try {
			DatastoreService datastore = DatastoreServiceFactory
					.getDatastoreService();
			return datastore.get(key);
		} catch (EntityNotFoundException e) {
			return null;
		}
	}

	public static void put(Entity entity) {
		DatastoreService datastore = DatastoreServiceFactory
				.getDatastoreService();
		datastore.put(entity);
	}

	public static void remove(Key key) {
		DatastoreService datastore = DatastoreServiceFactory
				.getDatastoreService();
		datastore.delete(key);
	}
	
}

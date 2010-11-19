package com.estontorise.lworm_php.gae;

import java.util.Iterator;

import com.google.appengine.api.datastore.DatastoreService;
import com.google.appengine.api.datastore.DatastoreServiceFactory;
import com.google.appengine.api.datastore.Entity;
import com.google.appengine.api.datastore.Query;
import com.google.appengine.api.datastore.Query.FilterOperator;
import com.google.appengine.api.datastore.Query.SortDirection;

public class GAEQueryAdapter {

	private final int FILTER_OP_EQUAL = 0;
	private final int FILTER_OP_GT = 1;
	private final int FILTER_OP_GT_EQUAL = 2;
	private final int FILTER_OP_LT = 3;
	private final int FILTER_OP_LT_EQUAL = 4;
	private final int FILTER_OP_NOT_EQUAL = 5;
	
	private final int SORT_ASC = 6;
	private final int SORT_DESC = 7;
	
	
	private Query query;

	public GAEQueryAdapter(String kind) {
		this.query = new Query("lworm_" + kind);
	}
	
	public GAEQueryAdapter(Query query) {
		this.query = query;
	}
	
	public GAEQueryAdapter addFilter(String field, int op, Object value) {
		FilterOperator operator = null;
		if(op == FILTER_OP_EQUAL)
			operator = FilterOperator.EQUAL;
		if(op == FILTER_OP_GT)
			operator = FilterOperator.GREATER_THAN;
		if(op == FILTER_OP_GT_EQUAL)
			operator = FilterOperator.GREATER_THAN_OR_EQUAL;
		if(op == FILTER_OP_LT)
			operator = FilterOperator.LESS_THAN;
		if(op == FILTER_OP_LT_EQUAL)
			operator = FilterOperator.LESS_THAN_OR_EQUAL;
		if(op == FILTER_OP_NOT_EQUAL)
			operator = FilterOperator.NOT_EQUAL;
		return new GAEQueryAdapter(query.addFilter(field, operator, value));
	}
	
	public GAEQueryAdapter addSort(String field, int dir) {
		SortDirection direction = null;
		if(dir == SORT_ASC)
			direction = SortDirection.ASCENDING;
		if(dir == SORT_DESC)
			direction = SortDirection.DESCENDING;
		return new GAEQueryAdapter(query.addSort(field, direction));
	}
	
	public Iterator<Entity> getEntityIterator() {
		DatastoreService ds = DatastoreServiceFactory.getDatastoreService();
		return ds.prepare(query).asIterable().iterator();
	}
}

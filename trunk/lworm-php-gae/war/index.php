<?php
	require_once "lib/lworm/ModelGenerator.class.php";
		
	$mg = new ModelGenerator();
	$mg->generateModel('test.yaml', 'model');
	
	require_once "lib/lworm/gae/GAEDataStoreFactory.class.php";
	require_once "model/User.class.php";
	require_once "model/Group.class.php";
	require_once "model/Role.class.php";
	
	$ds = GAEDataStoreFactory::getDataStore();

	// insert
	$user = new User;
	$user->setName("test");
	$ds->save($user);
	
	// update
	$user->setPassword("test");
	$ds->save($user);
	//var_dump($user);
	
	// read
	$user2 = $ds->getEntity(User, $user->getId());
	//var_dump($user2);
	
	// many to one
	$group = new Group;
	$group->setName("test");
	$ds->save($group);

	$user->getGroupRelation($ds)->setEntity($group);
	
	$user = new User;
	$user->setName("test 2");
	$ds->save($user);
	
	$user->getGroupRelation($ds)->setEntity($group);
	
	//var_dump($user->getGroupRelation($ds)->getEntity($group));
		
	// one to many
	
	//var_dump($group->getUsersRelation($ds)->getEntities());
	
	// many to many
	$role = new Role;
	$role->setName('Test Role 1');
	$ds->save($role);
	$user->getRolesRelation($ds)->addEntity($role);
	
	$role = new Role;
	$role->setName('Test Role 2');
	$ds->save($role);
	$user->getRolesRelation($ds)->addEntity($role);
	
	var_dump($user->getRolesRelation($ds)->getEntities());
	var_dump($role->getUsersRelation($ds)->getEntities());
	
	$user->getRolesRelation($ds)->removeEntity($role);
	var_dump($user->getRolesRelation($ds)->getEntities());
?>
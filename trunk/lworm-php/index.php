<?php
	require_once "lib/lworm/ModelGenerator.class.php";
	require_once "lib/lworm/mysql/MySQLDataStoreFactory.class.php";
		
	$mg = new ModelGenerator();
	$mg->generateModel('test.yaml', 'model');
	
	require_once "model/User.class.php";
	require_once "model/Role.class.php";
	require_once "model/Group.class.php";
	
	$ds = MySQLDataStoreFactory::getDataStore('localhost', 'lworm', 'root', '');
	$ds->createSchema('test.yaml');
	
	// many-to-many
	$user = new User;
	$user->setName('Test User');
	$ds->save($user);
	
	/*
	$role = new Role;
	$role->setName('Test Role');
	$ds->save($role);

	$roles = $user->getRolesRelation($ds);
	$roles->addEntity($role);
	//$roles->removeEntity($role);
	var_dump($roles->getEntities());
	*/
	
	// many-to-one
	$group = new Group;
	$group->setName("Test group");
	$ds->save($group);
	
	$user->getGroupRelation($ds)->setEntity($group);
	var_dump($user->getGroupRelation($ds)->getEntity());

	// one-to-many
	var_dump($group->getUsersRelation($ds)->getEntities());
?>
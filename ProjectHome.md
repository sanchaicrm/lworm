lworm is a lightweight ORM (Object Relational Mapping) tool for PHP, supporting Google App Engine Datastore and simple SQL architectures.

# Generating models #

Firstly, you need to define the entity structure in YAML format.

```
User: 
  fields: 
    name: text
    password: text
    active: boolean
    
  relations: 
    many-to-many: 
      roles: Role(user_roles)

    many-to-one: 
      group: Group

Role:
  fields:
    name: text
  relations: 
    many-to-many: 
      users: User(user_roles)

Group: 
  fields: 
    name: text
    
  relations: 
    one-to-many: 
      users: User(group)
```

Every entity is defined by the fields and relations. Fields are simple name-value pairs, defines the field name and type. Relations defines the relations between entities, which can be one-to-many, many-to-one and many-to-many. The relation is also defined by the name value pair, where the name is the name of the relation field, and the value is the referenced entity type.

If the relation type is one-to-many, you have to define a many-to-one relation in the referenced entity, and define the referring field in brackets. Look at the example:

```
User: 
  ...
  relations: 
    many-to-one: 
      group: Group

Group: 
  ...
  relations: 
    one-to-many: 
      users: User(group)
```

This example shows the definition of the 'group' relation field in the User entity, which points to the user's group. On the other side, Group has a 'users' field which points to the User entities where the group field is pointing to the Group.

Many to many relations uses similar method. When you define a many to many relation, you have to define a unique relation name.

```
User: 
  ...
  relations: 
    many-to-many: 
      roles: Role(user_roles)

Role:
  ...
  relations: 
    many-to-many: 
      users: User(user_roles)
```

In this example the User entity has more than one role. User entities are accessible from the Role, and the relation name is user\_role.

lworm has a model generator, which generates PHP entity class sources based on the YAML definition.

```
  require_once "lib/lworm/ModelGenerator.class.php";
	
  $mg = new ModelGenerator();
  $mg->generateModel('test.yaml', 'model');
```

This code will generate the model classes to the model folder, based on the test.yaml file. After the model generation, the classes can be included in the standard way:

```
 require_once "model/User.class.php";
 require_once "model/Role.class.php";
 require_once "model/Group.class.php";
```

# Using the datastore #

For saving, loading, removing and querying entities, you need to instantiate a datastore.

```
  $ds = MySQLDataStoreFactory::getDataStore('localhost', 'lworm', 'root', '');
  $ds->createSchema('test.yaml');
```

Every datastore type has its own Factory class. This example instantiates a MySQL datastore, and creates the database schema based on the YAML configuration.

# Using the entities #

The entity usage is really simple. Simply instantiate the entity object and set the properties. When the entity is saved by the datastore, it gets an id.

```
  $user = new User;
  $user->setName('Test User');
  $ds->save($user);
```

lworm entities are only memory objects (you can serialize them, store them in the session, transport them from/to the client side, etc.) For this reason if you change anything in the entity, you have to save it again to update the database.

```
  $user = new User;
  $user->setName('Test 2 User');
  $ds->save($user);
```

The datastore save method creates the entity if it hasn't id, and update it if the id is already set.

# Relations #

Relations are accessible through special methods of the entity. The relation method needs the actual datastore as a parameter because entities not exactly attached to any datastore. lworm supports three relation type.

## Many to One ##

In a many to one relation the source entity points to one foreign entity. This entity can be set/get by the relation methods.

```
  $user->getGroupRelation($ds)->setEntity($group);
  var_dump($user->getGroupRelation($ds)->getEntity());
```

## One to Many ##

One to many relation is the other side of the defined many to one relation. In this case it is only possible to get the related entities.

```
  var_dump($group->getUsersRelation($ds)->getEntities());
```

## Many to Many ##

In the many to many relation, the entity has a list of accessed entities. You can add the referenced entity, delete it, and get the list of assigned entities.

```
  $user->getRolesRelation($ds)->addEntity($role);
  var_dump($user->getRolesRelation($ds)->getEntities());
  $user->getRolesRelation($ds)->removeEntity($role);
```

# Query support #

lworm supports simple database queries through the query interface. You can filter and sort the result list. Look at this simple example:

```
  $ds->createQuery(User)
     ->addFilter('active', Query::FILTER_OP_EQUAL, 1)
     ->addSort('name', Query::SORT_DESC)
     ->getEntities()
```

# Google App Engine Datastore support #

It is possible to run PHP on GAE (Google App Engine) with Caucho Quercus, which is a pure Java implementation of PHP. One of the biggest problem when you want to move PHP applications from a standard LAMP environment to GAE is database access. If you use lworm, you can use the same ORM architecture on a standard LAMP environment and on GAE. The only difference is the usage of GAEDatastoreFactory.

```
  $ds = GAEDataStoreFactory::getDataStore();
```

lworm using the GAE Datastore Low-level API, which makes it really powerful.

# Extending lworm #

lworm defines 5 interfaces for datastore, query and relations. If you want to support other database architectures, you have to define an own factory, and implement these interfaces. If the architecture is SQL based, the extension is easier. In this case you have to only define a database adapter and an entity mapper which are only some lines of code.

```
  class MySQLDataStoreFactory {
		
    public static function getDataStore($db_host, $db_name, $db_user, $db_password) {
      return new SQLDataStore(new MySQLDatabaseAdapter($db_host, $db_name, $db_user, $db_password), new MySQLEntityMapper);
    }
  
  }
```

For more examples, look at the code in the SVN repository. It's really simple.

<a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LMQGC6YTEQKE4&item_name=Beer'>
<img src='http://www.paypal.com/en_US/i/btn/x-click-but04.gif' /><br />Buy me some beer if you like my code ;)</a>

If you like the code, look at my other projects on http://code.google.com/u/TheBojda/.

If you have any question, please feel free to contact me at thebojda AT gmail DOT com.
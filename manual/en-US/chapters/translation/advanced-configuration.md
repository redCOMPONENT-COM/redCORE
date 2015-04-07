## Translation advanced Configuration

Go to Joomla Plugins:

<img src="./assets/img/translation/31.png" class="example" />

Search for the redCORE plugin and click on it:

<img src="./assets/img/translation/04.png" class="example" />

Go to the Translation Tab:

<img src="./assets/img/translation/05.png" class="example" />

There you will find a special configuration parameter for translations:

<img src="./assets/img/translation/30.png" class="example" />

- **Foreign keys (InnoDB)** is the DEFAULT option and the best option if your database server supports InnoDB (http://dev.mysql.com/doc/refman/5.0/en/innodb-storage-engine.html). Using this option, redCORE will create tables connected using MySQL InnoDB foreign key constraints, so when some row from original table gets deleted, all keys from shadow table will get deleted as well (from all languages).
- **Triggers** is also good option, but you have to have a full access to your database (your database user) to be able to use this feature. Selecting this option, redCORE will trigger on delete an action to delete all keys from shadow table.
- **none** option is not recommended, it will not delete rows from translated strings when the original item is been deleted. You will not be able to access them from editor too so they may pile up if you are constantly deleting items and translating them.


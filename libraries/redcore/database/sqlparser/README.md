php-sql-parser
==============
This project comes originally from http://code.google.com/p/php-sql-parser/. But there wasn't an update during the whole last year. So I converted this pretty piece of code and made it ready for PSR-0 autoloaders.

With this parser you can parse at least MySQL queries very easy an extract data from the query you need.

```php


$sqlParser = new RDatabaseSqlparserSqlparser("SELECT * FROM table WHERE ID IN(5,7,8)");
var_dump($sqlParser->parsed);

/* EOF */
```

The project ist under the New BSD License.

Cloned from https://github.com/TiMESPLiNTER/php-sql-parser

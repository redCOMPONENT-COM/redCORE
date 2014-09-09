## getAutoInstance

```
<?php
$productTable = RTable::getAutoInstance('Product');
```

will get an instance of the backend or frontend table `RedshopTableProduct` if the option is `com_redshop`.

## getAdminInstance

```
<?php
$productTable = RTable::getAdminInstance('Product');
```

will get an instance of the **backend** table `RedshopTableProduct` if the option is `com_redshop`.

## getFrontInstance

```
<?php
$productTable = RTable::getFrontInstance('Product');
```

will get an instance of the **frontend** table `RedshopTableProduct` if the option is `com_redshop`.
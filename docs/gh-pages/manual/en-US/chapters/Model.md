## getAutoInstance

```
<?php
$productTable = RModel::getAutoInstance('Configuration');
```

will get an instance of the backend or frontend model `RedshopModelConfiguration` if the option is `com_redshop`.

```
<?php
$productTable = RModel::getAutoInstance('Configuration', null, array(), 'com_redshop');
```

will get an instance of the backend or frontend model `RedshopModelConfiguration` of redSHOP component. (Use on module or other components)

## getAdminInstance

```
<?php
$productTable = RModel::getAdminInstance('Configuration');
```

will get an instance of the **backend** model `RedshopModelConfiguration` if the option is `com_redshop`.

```
<?php
$productTable = RModel::getAdminInstance('Configuration', array(), 'com_redshop');
```

will get an instance of the **backend** model `RedshopModelConfiguration` of redSHOP component. (Use on module or other components)

## getFrontInstance

```
<?php
$productTable = RModel::getFrontInstance('Configuration');
```

will get an instance of the **frontend** model `RedshopModelConfiguration` if the option is `com_redshop`.

```
<?php
$productTable = RModel::getFrontInstance('Configuration', array(), 'com_redshop');
```

will get an instance of the **frontend** model `RedshopModelConfiguration` of redSHOP component. (Use on module or other components)
To use redCORE layouts with frontend frameworks a prefix is needed in each `.php` file.
```php
if ($framework == 'bootstrap3')
{
    self::$frameworkSuffix = 'bs3';
    self::$frameworkOptions = array(
        'disableMootools' => true,
    );
}
elseif ($framework == 'bootstrap2')
{
    self::$frameworkSuffix = 'bs2';
    self::$frameworkOptions = array(
        'disableMootools' => false,
    );
}
elseif ($framework == 'foundation5')
{
    self::$frameworkSuffix = 'fd5';
    self::$frameworkOptions = array(
        'disableMootools' => false,
    );
}
else
{
    self::$frameworkSuffix = '';
    self::$frameworkOptions = array(
        'disableMootools' => false,
    );
}
```

The default one is bootstrap3 so if the current template is for example bootstrap2, one must add `.bs2` prefix to all `.php` files, example: `example.bs2.php`.
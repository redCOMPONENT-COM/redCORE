redCORE integrates [Select2](http://ivaynberg.github.io/select2/) support for select boxes enhacement.

To load select2 in any page add:


```
<?php 
JHtml::_('rjquery.select2', 'select');
```
That will load select2 and tie it to any select box. You can also add a more specific the selector like:

```
<?php 
JHtml::_('rjquery.select2', '.select2');
```

That will load select2 to all the select boxes with the class `.select2`

This is the full function definition for reference.

```
<?php 
public static function select2($selector = '.select2', $options = null, $bootstrapSupport = true)
```

The `$options` parameter allows you to customise the options of the select2 field. This is the [official list of select2 parameters](http://ivaynberg.github.io/select2/#documentation)

For example to use the same width of the parent select box:

```
<?php 
JHtml::_('rjquery.select2', '.select2', array('width' => 'element'));
```
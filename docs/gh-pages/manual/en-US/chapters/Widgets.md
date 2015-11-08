## <a name="tooltip"></a>Tooltip

redCORE uses [Twitter Bootstrap tooltip system](http://twitter.github.io/bootstrap/javascript.html#tooltips).

To use tooltips in any component we will use this loader:

```js+php
JHtml::_('rbootstrap.tooltip');
```

You can customise the tooltip with options. This is the standard function call:  

```js+php
public static function tooltip($selector = '.hasTip', $params = array())
```

By default it will use the selector ".hasTip" (is the same used in Joomla! core). If you want to use tooltips with other classes you can use:  

```js+php
JHtml::_('rbootstrap.tooltip', '.my-tooltip');
```

The tooltip script also supports other parameters. This are the options supported by bootstrap:  

[![Tooltip options](./images/tooltip_options.png "Tooltip options")](./images/tooltip_options.png)  

This is a sample call with custom options that places the tooltips at the right side of the object:  

```js+php
JHtml::_('rbootstrap.tooltip', '.hasTip', array('placement' => 'right'));
```
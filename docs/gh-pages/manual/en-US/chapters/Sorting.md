redCORE integrates the standard Joomla! sorting but using layouts to render the Html. So you can customise the layout per component without having to deal with it.

### Normal sorting

This is the most used case for the title of table columns.

```
<?php echo JHtml::_('rgrid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
```

### Sorting for ordering column

Special example of the ordering column that only displays an icon.

```
<?php echo JHtml::_('rgrid.sort', null, 'a.ordering', $listDirn, $listOrder, null, 'asc', '', 'icon-sort'); ?>
```
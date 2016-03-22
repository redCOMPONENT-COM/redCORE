redCORE integrates Chosen support for select boxes enhacement.

To load chosen in any page add:

```
<?php 
JHtml::_('rjquery.chosen', 'select');
```

That will load chosen and tie it to any select box. You can also add a more specific the selector like:

```
<?php 
JHtml::_('rjquery.chosen', '.chosen');
```

That will load chosen to all the select boxes with the class .chosen.
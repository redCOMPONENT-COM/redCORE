## ControllerForm

### Automatic return

You can force the form controller to redirect to a specific url after save/close by passing the base64 encoded return url in the request :

```
<?php

$return = base64_encode('index.php?option=com_redshopb&view=product&layout=edit&id=' 
. $productId);
$url = JRoute::_('index.php?option=com_redshopb&task=product.edit&id=' 
. $item->id . '&return=' . $return); ?>
```

in the form item you must add :

```
<input type="hidden" name="return" 
value="<?php echo JFactory::getApplication()->input->get('return') ?>">
```
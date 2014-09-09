## Topbar

`$data['display_joomla_menu']` : boolean to display the joomla menu in the toolbar.

Note : the menu is very dirty under Joomla 2.5 as it's not bootstraped.

## Modal

### Arguments

- `attribs` : Attributes for the modal element
- `showHeader` : True to show the modal header
- `showFooter` : True to show the modal footer
- `showHeaderClose` : True to show the 'x' button in the modal header
- `title` : The modal title
- `link` : The modal inner link to load

### Example

```
<?php
$modal = RModal::getInstance(
    array(
      'attribs' => array(
                    'id' => $modalId,
                    'class' => 'modal hide fade',
                    'style' => 'width: 700px; height: 500px;'
                    ),
      'params' => array(
      'showHeader' => true,
      'showFooter' => false,
      'showHeaderClose' => true,
      'title' => $modalTitle,
      'link' => $link
      )
    ),
$modalId
);

// Render the modal
echo RLayoutHelper::render('modal', $modal);
```
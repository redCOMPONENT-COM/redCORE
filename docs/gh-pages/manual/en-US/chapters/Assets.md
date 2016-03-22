Use RHelperAsset to load JS, CSS & images. That will automatically allow users to override that files in the template and makes asset loading quite simple.

```
<?php
/**
 * Load JS
 *
 * Searches files in this order (when called inside com_helloworld component):
 *
 * /templates/MY_TEMPLATE/js/com_helloworld/vendor/chosen.jquery.js
 * /media/com_helloworld/js/vendor/chosen.jquery.js 
 *
 */
RHelperAsset::load('vendor/chosen.jquery.js');
 
/**
 * Load CSS
 *
 * Searches files in this order:
 *
 * /templates/MY_TEMPLATE/css/mod_menu.css
 * /media/mod_menu/css/menu.css
 *
 */
RHelperAsset::load('menu.css', 'mod_menu');
 
/**
 * Load Image
 *
 * Searches for images in this order:
 *
 * /templates/MY_TEMPLATE/images/mod_product_search/search.png
 * /media/mod_product_search/images/search.png
 *
 */
echo RHelperAsset::load('search.png', 'mod_product_search', array('alt' => 'This is the alt text'));
```
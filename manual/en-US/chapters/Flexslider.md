redCORE integrates [flexslider](https://github.com/woothemes/flexslider) support for creating image slider.

To load flexslider libraries in any page add:


```
<?php 
JHtml::_('rjquery.flexslider');
```
That will load basic flexslider libraries and tie it to all divs with class `flexslider`.
For specific selector, use following:

```
<?php 
JHtml::_('rjquery.flexslider', '.example-class');
```
Where `example-class` is wanted selector.
You can also provide options array with specific settings for slider.

```
<?php 
JHtml::_('rjquery.flexslider', '.example-class', array('animation' => 'slide'));
```
This way, slider `animation` will be `slide`.

For more info on available slider options checkout [this page](https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties)

This is the full function definition for reference.

```
<?php 
public static function flexslider($selector = '.flexslider', $options = null)
```

After loading libraries you can use flexslider with following:
```html
<!-- Place somewhere in the <body> of your page -->
<div class="flexslider">
  <ul class="slides">
    <li>
      <img src="slide1.jpg" />
    </li>
    <li>
      <img src="slide2.jpg" />
    </li>
    <li>
      <img src="slide3.jpg" />
    </li>
    <li>
      <img src="slide4.jpg" />
    </li>
  </ul>
</div>
```
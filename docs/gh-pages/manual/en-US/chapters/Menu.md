Breadcrumb Example :

```
// Create the root node.
$home = new RMenuNode('home', 'Home', '#');

// Create a product node children of home.
$product = new RMenuNode('product', 'Product', '#');
$home->addChild($product);

// Create a category node children of home.
$category = new RMenuNode('category', 'Category', '#');
$home->addChild($category);

// Create a category_attributes node children of category.
// Set this node active.
$categoryChild = new RMenuNode('category_attributes', 'Category Attributes', '#');
$categoryChild->setActive();
$category->addChild($categoryChild);

// Create the tree with home as root node.
$tree = new RMenuTree($home);

// Add the tree to the menu
$menu = new RMenu;
$menu->addTree($tree);

// Render a breadcrumb.
echo RLayoutHelper::render('menu.breadcrumb', array('menu' => $menu));
```
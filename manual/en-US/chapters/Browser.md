## Usage

```
<?php

// Will create a context 'redshop.checkout' and browse the current uri
RBrowser::getInstance('redshop.checkout')->browse();

// Getting a browser instance
$browser = RBrowser::getInstance('redshop.checkout');

// Will remove the currently browsed uri and return it
$currentUri = $browser->back();

// Get the currently browsed uri
$currentUri = $browser->getCurrentUri();

// Get the currently browsed view
$currentView = $browser->getCurrentView();

// Get the last browsed uri
$lastUri = $browser->getLastUri();

// Get the last browsed view
$lastView = $browser->getLastView();

// Clear the browser history
$browser->clearHistory();
```
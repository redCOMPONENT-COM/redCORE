To set up the redCORE translations system install redCORE in Joomla.

## Activating redCORE translations

Open the translation feature at `Components -> redCORE -> Translations`:

![image](./assets/img/translation/01-redcore-translations-menu.png.png" style="width:85%;">



When loading the translation feature for first time you will see a warning message in red that reminds you to publish the translation plugin.

<img src="./assets/img/translation/02-redcore-translations-plugin.png" style="width:85%;">



Click in the `Configure` link at the message:
<img src="./assets/img/translation/03-redcore-translations-plugin-link.png" style="width:85%;">



That will take you to the Plugins list, click once more in the Plugin title to edit it:

<img src="./assets/img/translation/04-redcore-translations-plugin-list.png" style="width:85%;">



Then click at the `Translation options` tab:

<img src="./assets/img/translation/05-redcore-translations-plugin-tabs.png" style="width:85%;">



The last step consists:

<img src="./assets/img/translation/06-redcore-translations-plugin-options.png" style="width:85%;">



You can now go back to redCORE translations  `Components -> redCORE -> Translations`:

<img src="./assets/img/translation/01-redcore-translations-menu.png" style="width:85%;">



## Creating Content Languages in Joomla
Before you can translate any content you will have to specify in Joomla witch are the available languages.

Go to `Extensiosn -> Language Manager`:

<img src="./assets/img/translation/07.png" style="width:85%;">



The first step is to install the language that you want to use:

<img src="./assets/img/translation/09.png" style="width:85%;">



In the following example we are installing the Spanish:

<img src="./assets/img/translation/10.png" style="width:85%;">



What you have installed is just a set of Joomla basic words already translated to Spansish that will be used for the Joomla interface, for example the common words used at the buttons of the toolbar: save, save adn close, publish / unpublish...

Once Spanish is available in the system you will have to set up a Content Language. The 'Content language' screen is the place in Joomla were you define how and witch languages will be your content translated to.

In the following example we are going to create the Spanish as Content language. First you have to click at `Content`:

<img src="./assets/img/translation/08.png" style="width:85%;">



Then click at `New`:

<img src="./assets/img/translation/11.png" style="width:85%;">



And fill the form according to the language specifications:

<img src="./assets/img/translation/12.png" style="width:85%;">



Once you `Save & Close`, you will be able to see a second language in your `Content languages` list:

<img src="./assets/img/translation/13.png" style="width:85%;">



## Publishing the Language switcher module
The next step consist in publish the Language Swticher Module in the Frontpage:
<img src="./assets/img/translation/19.png" style="width:85%;">



Go to `Extensions -> Module Manager`:

<img src="./assets/img/translation/14.png" style="width:55%;">



Find the `redCORE language switcher` and click on it:

<img src="./assets/img/translation/16.png" style="width:85%;">



Once you are in the Module edit screen do the following steps:

- publish the module

<img src="./assets/img/translation/17.png" style="width:55%;">



- place it on all pages:

<img src="./assets/img/translation/18.png" style="width:85%;">



- And make sure you place it in a proper position in your template. If you are using Joomla defaults template use the `position-7`

At the end of this process you should be able to go to your website and see the Language switcher:

<img src="./assets/img/translation/19.png" style="width:85%;">




## Translating the home menu item
Now Joomla and redCORE is ready to start translating. Our first translation will be the `home` button:

<img src="./assets/img/translation/20.png" style="width:85%;">



Go to `redCORE Translations`:

<img src="./assets/img/translation/01-redcore-translations-menu.png" style="width:85%;">



To translate the Home menu button follow the following steps:

- Select `menus`in the type `Select filter`
- Chosse the language to translate into in the `language filter`
- You will see the **Home** button appearing in the list, click on it to edit

<img src="./assets/img/translation/21.png" style="width:85%;">



Create the translation and publish it:

<img src="./assets/img/translation/22.png" style="width:85%;">



Save the changes and test in your website if your `Home` button is now translated:

<img src="./assets/img/translation/23.png" style="width:85%;">





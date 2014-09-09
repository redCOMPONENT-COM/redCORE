This uses [UglifyJS](https://github.com/mishoo/UglifyJS) to create minified files.  

## Install node:

```bash
# python-software-properties is needed to use add-apt-repository
sudo apt-get install python-software-properties python g++ make
sudo add-apt-repository ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs
```

## Install uglifyjs

Bootstrap requires a specific `uglifyjs` version that works with bootstrap:

```bash
sudo npm install uglify-js@1 -g
```

## Sample minify command

To minify bootstrap `redCORE` files:

```bash
uglifyjs -nc media/redcore/js/lib/bootstrap.js > media/redcore/js/lib/bootstrap.min.js
```
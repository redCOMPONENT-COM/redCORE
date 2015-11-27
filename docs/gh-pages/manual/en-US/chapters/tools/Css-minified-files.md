# CSS minified files

We recommend using Gulp for redCORE development instead of minifying files manually but if you cannot use Gulp for some reason, you can create minified CSS files by running standalone tool.

## Yuicompressor

Download the last release of [YUI Compressor](https://github.com/yui/yuicompressor/releases).

`java -jar yuicompressor-x.x.x.jar component.css -o component.min.css --charset utf-8`

### Ubuntu

#### Install

`sudo apt-get install yui-compressor`

#### Use

```
yui-compressor build/media/redcore/css/component.css -o extensions/media/redcore/css/component.min.css --charset utf-8
```

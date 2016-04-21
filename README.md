redCORE
======

[![Download latest](https://img.shields.io/badge/Download-stable-brightgreen.svg)](https://github.com/redCOMPONENT-COM/redCORE/releases/latest) [![Travis Build Status](https://travis-ci.org/redCOMPONENT-COM/redCORE.svg?branch=develop)](https://travis-ci.org/redCOMPONENT-COM/redCORE) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/redCOMPONENT-COM/redCORE/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/redCOMPONENT-COM/redCORE/?branch=develop)

# Overview

redCORE is a RAD (Rapid Application Development) Layered abstraction layer that focuses on Development of complex extensions.

redCORE focuses on allowing the more complex structures and thus saving time for the developer so the focus becomes on added value and not in reinventing the Wheel.

The main aim of redCORE is to provide a mature and abstracted layer for development that will act as a base model for any future redCOMPONENT extension being developed.

There is no convention over configuration in redCORE, unlike its counterparts, because we need more complex structures and hierarchy and to solve more complex problems in redCORE.

redCORE is a quicker and more uniform way of creating extensions while adding some very interesting libraries and features to Joomla.

redCORE is not a rapid application development tool based on conventions to automatically create output.

redCORE based extensions works for Joomla 2.5 and 3.x.

It is our hope that redCORE will be interesting for other Joomla developers or even the Joomla Core.

Regards,

Ronni K. Gothard Christiansen

Director and Founder of redCOMPONENT.com

CEO, redWEB ApS

## Contributing
See: [Contributing to redCORE](http://redcomponent-com.github.io/redCORE/?chapters/Contributing.md)

## Documentation
See: [redCORE Documentation](http://redcomponent-com.github.io/redCORE/)

## Testing
See: [testing redCORE](./tests/README.md)

## Repository structure

Structure is based on maximum simplicity for developers and for end users:

- build - Folder where we keep all our tools that we use in this repository (PHING, Gulp, CodeSniffer, LESS, JS files, ...). Additionally here is where we keep uncompressed media files which we can compress (minify) and move to the extensions folder
- docs - Folder is where we keep all information for support and is a place where we keep github documentation pages
- extensions - Folder where we keep all the files that will be installed with the package already minified and ready to be installed. If pointed to that folder one could install extension from that folder
- tests - Folder that is used for automated testing of the redCORE

See more information in: [redCORE Folder Structure](http://redcomponent-com.github.io/redCORE/?chapters/folder-structure.md)


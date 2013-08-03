OAuth 2.0 for Joomla Api Application
====================================

	1. Introduction
	2. Instalation
	3. How it works


1. Introduction
---------------

The OAuth 2.0 for Joomla Api Application is based on a Louis Landry work about 
oauth1 server suport for Joomla! Platform.

```
https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
```
Also this library is based on RFC 6849 (http://tools.ietf.org/html/rfc6749)


This is the basic graph of how is the process of the authentication using OAuth 2.0:

```
 +-------------------------+                                         +-----------------------------+
 |      Client             |                                         |         Server              |
 |-------------------------|                                         |-----------------------------|
 |                         |                                         |                             |
 | Request temporary token.| +---------------GET------------------>  | Receive request and send    |
 |                         |                                         | the temporary token.        |
 | Get the temporary token | <-JSON-------------------------------+  |                             |
 | and send authorization  |                                         |                             |
 | request.                | +---------------POST----------------->  | Authorise or deny and return|
 |                         |                                         | the status and new temporary|
 | Get the status and the  | <-JSON-------------------------------+  | token.                      |
 | new temporary token,    |                                         |                             |
 | then request the access | +---------------POST----------------->  | Compare temporary token and |
 | token.                  |                                         | authentication and return   |
 |                         |                                         | the access token.           |
 | Get the access token for| <-JSON-------------------------------+  |                             |
 | request protected       |                                         |                             |
 | resources.              |                                         |                             |
 |                         |                                         |                             |
 |                         |                                         |                             |
 +-------------------------+                                         +-----------------------------+
```

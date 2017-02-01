# Api tests

The tests inside this folder are structured according to the following convention in its name:
 
`Application`.`extension`-`item`.`version`-`test type`.Cest.php

Examples:
 
- site.redcore-category.v1.0.0-01availability.Cest.php
- administrator.contact-contact.v1.0.0-01availability.Cest.php 

The generic test types are:

- `01availability`: to check if webservice is available
- `02structure`: creates one item at checks the structure of the returned item
- `03crud`: contains basic Create, readItem, readList, Update and Delete
- `04filtering`: tests readList possible filters: by order, by search...
- `05tasks`: Publish, Unpublish and other specific tasks of the webservice
- `06erp`: erp integration tests with external systems
- `07soap`: API tests using SOAP
- ... 
## Output for Read List

Output for one specific HAL list document looks like this:

```
{
  "_links": {
    "curies": [
      {
        "href": "http://YOUR-SITE/administrator/index.php?option=contact&format=doc&api=Hal#{rel}",
        "title": "Documentation",
        "name": "contact",
        "templated": true
      }
    ],
    "base": {
      "href": "http://YOUR-SITE/administrator/?api=Hal",
      "title": "Default page"
    },
    "contact:self": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&api=Hal"
    },
    "contact:first": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limitstart=0&api=Hal",
      "title": "Pagination first"
    },
    "contact:previous": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limitstart=0&api=Hal",
      "title": "Pagination previous"
    },
    "contact:next": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limitstart=2&api=Hal",
      "title": "Pagination next"
    },
    "contact:last": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limitstart=10&api=Hal",
      "title": "Pagination last"
    },
    "contact:all": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limit=0&api=Hal",
      "title": "Show all items"
    },
    "contact:limit": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&limit={limit}&limitstart={limitstart}&api=Hal",
      "title": "List limit",
      "templated": true
    },
    "contact:filter": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&filter_search={filter_search}&api=Hal",
      "title": "Filter list",
      "templated": true
    },
    "contact:sort": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&filter_order={filter_order}&filter_order_Dir={filter_order_Dir}&api=Hal",
      "title": "List sort",
      "templated": true
    }
  },
  "page": "1",
  "pageLimit": "2",
  "limitstart": "0",
  "totalItems": "12",
  "totalPages": "6",
  "_embedded": {
    "contacts": [
      {
        "_links": {
          "contact:self": {
            "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&api=Hal"
          },
          "categories": {
            "href": "http://YOUR-SITE/administrator/index.php?option=categories&id=4&api=Hal"
          }
        },
        "id": "1",
        "name": "test contact 1",
        "featured": "0",
        "language": "*"
      },
      {
        "_links": {
          "contact:self": {
            "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=3&api=Hal"
          },
          "categories": {
            "href": "http://YOUR-SITE/administrator/index.php?option=categories&id=4&api=Hal"
          }
        },
        "id": "3",
        "name": "test contact 2",
        "featured": "0",
        "language": "en-GB"
      }
    ]
  }
}
```

This is standard list output for Read Operation. 

This resources are defined in the webservice XML mapping file.

### List operations 

The redCORE webservice API supports few native List operations:

1. Pagination First - when visiting this link it will show you list from the first item

2. Pagination Previous - when visiting this link it will take you to the previous page or first page if it does not have any previous pages

3. Pagination Next - when visiting this link it will take you to the next page of list items or the last page if it does not have any pages left

4. Pagination Last - when visiting this link it will show you list from the last page of the list

5. Pagination All - when visiting this link it will show you all list items without any pagination limitation

6. Pagination Limit - you can use templated link `limit` to set number of items per page with {limit} parameter and you can fetch specific page by setting starting page of your list by using {limitstart} parameter

7. Filter list - you can use templated link `filter` to filter your list by specific parameter Ex. `&filter_search={filter_search}` where you replace `{filter_search}` with your search parameter

8. Sort list - you can use templated link `sort` to sort your list by specific parameter 
Ex. `&filter_order={filter_order}&filter_order_Dir={filter_order_Dir}` where you replace `{filter_order}` with your sort direction parameter to ASC or DESC, and `{filter_order}` with your field name sort parameter

Other `_links` in the Item document can be used as a links to other resource documents or a link to the specific tasks.

### List data resources

List data resources mainly consists of list descriptive data such as 

`page` is the number of page the user is currently on (it starts from 1)

`pageLimit` is a limit for the number of items per page

`limitstart` is a starting number of items on the current page (it starts from 0)

`totalItems` is a total number of items in the list

`totalPages` is a total number of pages that client can browse in this list using current `pageLimit`

### List embedded resources

Embedded resources are list item currently available on the list page.

Embedded items have a limited resources and are used mainly to identify the items. In this example we have 2 embedded items and they have their own `_links` and data resources



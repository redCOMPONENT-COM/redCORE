## Output for Read Item

Output for one specific HAL item document looks like this:

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
      "href": "http://YOUR-SITE/administrator/?webserviceClient=administrator&api=Hal",
      "title": "Default page"
    },
    "contact:self": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&api=Hal"
    },
    "contact:list": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&api=Hal"
    },
    "categories": {
      "href": "http://YOUR-SITE/administrator/index.php?option=categories&id=4&api=Hal"
    },
    "contact:checkout": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=checkout&api=Hal"
    },
    "contact:checkin": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=checkin&api=Hal"
    },
    "contact:featured": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=featured&api=Hal"
    },
    "contact:unfeatured": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=unfeatured&api=Hal"
    },
    "contact:publish": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=publish&api=Hal"
    },
    "contact:unpublish": {
      "href": "http://YOUR-SITE/administrator/index.php?option=contact&id=1&task=unpublish&api=Hal"
    }
  },
  "id": "1",
  "name": "test contact 1",
  "alias": "test-contact-1",
  "con_position": "a",
  "address": "1\r\My street",
  "suburb": "111",
  "state": "",
  "country": "Croatia",
  "postcode": "10000",
  "telephone": "1",
  "fax": "",
  "misc": "<p>sample test</p>",
  "image": "",
  "email_to": "test@test.com",
  "default_con": "0",
  "published": "1",
  "checked_out": "0",
  "checked_out_time": "0000-00-00 00:00:00",
  "ordering": "1",
  "params": "{\"show_contact_category\":\"\",\"show_contact_list\":\"\",\"presentation_style\":\"\",\"show_tags\":\"\",\"show_name\":\"\",\"show_position\":\"\",\"show_email\":\"\",\"show_street_address\":\"\",\"show_suburb\":\"\",\"show_state\":\"\",\"show_postcode\":\"\",\"show_country\":\"\",\"show_telephone\":\"\",\"show_mobile\":\"\",\"show_fax\":\"\",\"show_webpage\":\"\",\"show_misc\":\"\",\"show_image\":\"\",\"allow_vcard\":\"\",\"show_articles\":\"\",\"show_profile\":\"\",\"show_links\":\"\",\"linka_name\":\"\",\"linka\":false,\"linkb_name\":\"\",\"linkb\":false,\"linkc_name\":\"\",\"linkc\":false,\"linkd_name\":\"\",\"linkd\":false,\"linke_name\":\"\",\"linke\":\"\",\"contact_layout\":\"\",\"show_email_form\":\"\",\"show_email_copy\":\"\",\"banned_email\":\"\",\"banned_subject\":\"\",\"banned_text\":\"\",\"validate_session\":\"\",\"custom_reply\":\"\",\"redirect\":\"\"}",
  "user_id": "0",
  "catid": "4",
  "access": "1",
  "mobile": "2123321321",
  "webpage": "",
  "sortname1": "",
  "sortname2": "",
  "sortname3": "",
  "language": "*",
  "created": "2014-10-12 07:50:12",
  "created_by": "914",
  "modified": "0000-00-00 00:00:00",
  "modified_by": "0",
  "metakey": "",
  "metadesc": "",
  "metadata": "{\"robots\":\"\",\"rights\":\"\"}",
  "featured": "0",
  "publish_up": "0000-00-00 00:00:00",
  "publish_down": "0000-00-00 00:00:00",
  "version": "1",
  "hits": "0"
}
```

This is standard item output for Read Operation. 

This resources are defined in the webservice XML mapping file.

### Item _links resources 

In the above example we can identify few of the most common `_links` resources:

1. `base` is a link to Default page of the site

2. `self` is a link to this item document

3. `list` is a link to the item List of this webservice

Other `_links` in the Item document can be used as a links to other resource documents or a link to the specific tasks. 
Ex. `contact:unpublish` can set current item to `Unpublished` state.

### Item data resources

Item data resources mainly consists of resources for this specific item. 

`id` is the primary key of the item

`name` is a name of that item

...


### Item embedded resources

Embedded resources can be shown if there exists a child item for your current item. 

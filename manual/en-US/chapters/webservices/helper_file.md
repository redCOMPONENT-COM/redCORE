## Webservice helper file

With every webservice you can add your own `helper file` that will be loaded at the same time your webservice is called.

### How it must be formatted?

Helper files must have exact same name as the webservice they belong.
So if the webservice file name is `administrator.contact.1.0.0.xml` then your helper file must be named the same `administrator.contact.1.0.0.php`.

Class name must begin with `RApiHalHelper` then if your webservice is for `administration` then you have to add `Administrator` to the Class name,
likewise if your webservice is for `site` then you have to add `Site` to the Class name.
Last name you have to add is your webservice name to the Class name (ex. Contact) and it have to start with Capital letter without any special characters.

Final look of the class name would be like this:

```
// This is helper file for Contact webservice in Administration
class RApiHalHelperAdministratorContact
{
}
```

### How it is used?

Helper file may contain various methods that is used in webservice `API` to parse data or to complete replace original method from the API.
The rule is simple, if the method exists in helper file, it will be called instead of original `API` method.

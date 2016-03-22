## <a name="rpublished"/></a> RPublished

### Description
Field to show a select list of statuses. It uses the default Joomla! statuses values:  

* 1   => Published
* 0   => Unpublished
* 2   => Archived
* -2  => Trashed
* '*' => All

You can customise which status show/enable with the attribute <code>statuses</code>.

### Sample code

This field will only show statuses 0,1 (unpublished, published):  

```
<field 
   name="filter_published" 
   type="rpublished"
   label="JFIELD_LANGUAGE_LABEL"
   description="COM_JAB_FIELD_LANGUAGE_DESC"
   statuses="0,1"
   class="chosen"
   onchange="this.form.submit();"
>
   <option value="">JOPTION_SELECT_PUBLISHED</option>
</field>
```
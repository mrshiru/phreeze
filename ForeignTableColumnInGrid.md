# Introduction #

The generated Phreeze app includes one "Grid" per database table.  This is mostly meant as a starting point so that the boring parts are done for you, but you will want to customize the columns.

One common feature for a relational database is to include a column from another table into a grid.  For example you may want to show the Name of the person who created a record instead of showing their use ID.

# Details #

Assuming you have two tables: Customer and Purchase.  These each have their own controller CustomerController and PurchaseController.

The Purchase table has a field CustomerId which refers to the Customer table.  The default Purchase grid displays the CustomerId column, however we want to show the Name column from the Customer table.  So how do we combine the two tables into one grid?

There are two ways to do it.  This tutorial will go into a simplified method.

## Step One: Add the additional field to the XML datasource ##

The Purchase Grid pulls it's data via AJAX and the method that it calls is libs/PurchaseController->ListPage.  This is the default naming that classbuilder uses.

If you look inside ListPage you'll see the last line is:

$this->RenderXML($datapage);

If you look at the docs for RenderXML, there are additional parameters that allow you to add or subtract from the fields that will be included in the XML output.  We are going to tell RenderXML to include one field from our foreign table like so:

$this->RenderXML($datapage, Array("GetCustomer"=>"Name"));

The second parameter is an associative array of relational fields to include.  In this case we are telling RenderXML on every iteration of the Purchase objects to call a method and include that in the XML.

$purchase->GetCustomer()->Name will be called and included in the XML as GetCustomerName

You can view index.php?action=Purchase.ListPage to see this XML directly in order to debug.

## Step One: Add the additional columns to the Grid ##

Once the additional columns you want are in your XML that is used by the Grid, the rest of the task is not really specific to Phreeze, but rather is just configuring the Ext Grid.  To do this you'll need to open templates/ViewPurchaseListAll.tpl and edit the javascript

The javascript has two arrays that are used by the Grid called readerDef and columnDef.  readerDef is an array that maps the server XML to the dataStore.

columnDef is the definition of columns in the Grid and points each column to its source data using the dataIndex property.

So, with our above example you would need to add the following to readerDef:

var readerDef = [
...
{name: 'GetCustomerName', mapping: 'GetCustomerName'}
...
];

(The reason these properties are the same is because we are simply using the same property names for the datastore as the element names in the XML.)

So now this extra XML data has been mapped to the datastore, so we need to add the column to the columnDef, which will make it appear in the grid:

var columnDef = [
...
{header: 'Customer', dataIndex: 'GetCustomerName', width: 150, sortable: false}
...
];


I've set "Sortable" to false is this case because it won't sort properly using this simple method.

If you need more robust functionality, the recommended solution is to write a custom Reporter object and use that instead of a basic Phreezable object.
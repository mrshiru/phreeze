{ldelim}include file="_header.tpl" title="{$connection->DBName|studlycaps} Home"{rdelim}

<div><img src="http://www.phreeze.com/images/phreeze_banner.jpg" alt="Phreeze Framework" /></div>

<h2>Welcome!</h2>

<p>
This code has been automatically generated by the 
<a href="http://www.phreeze.com/">Phreeze Framework</a> for PHP.
Phreeze provides basic read/write operations so that you don't have to write the same
repetitive code every time you create a new application.  Though everything
in this application works, it is currently only a simple data editing application.
This may be fine for certain internal utility apps, but most likely you should
use this code only as a starting point for creating your application.
</p>

<h2>Customizing Your Application</h2>

<p>The application directory structure contains several generated files that
you will want to alter to suit your own needs.  The Phreeze libraries themselves
don't need to be altered, however the generated files in your application
directory can all be modified to suit your needs.  Some of the files and folders you 
probably will want to check out first are:</p>

<ul>
<li>~/_config.php (This file contains your configuration settings)</li>
<li>~/libs/Controller/ (This folder contains the Controllers)</li>
<li>~/libs/Model/ (This folder contains your models where you should add business logic)</li>
<li>~/libs/Model/DAO/ (This folder contains object-to-database mapping information)</li>
<li>~/templates/  (This folder contains your Views)</li>
</ul>

<h2>The Model</h2>

<p>The Model provides access to the database so that you can read/write data using
PHP objects and functions rather than writing SQL code.  This is often called an
ORM and Phreeze includes simple ORM that gives you the basics, but doesn't get
in your way for more advanced SQL.</p>

<p>If your child relationship names all funky, note that classbuilder uses the names
of your Foreign Keys to create names for the relationship.  Many people don't bother
to name their foreign keys and so the code generated by classbuilder may have some
funky names that you don't recognize.  For best use, update the name of your 
keys and indexes.  This should have no effect on your database and it will help
classbuilder to generate meaningful names for your relationships.</p>

<h2>The Controller</h2>

<p>The Controller is the part of the appliation that receives input from the user,
processes the input, and then decides what View to present back to the user.
You access your pages using the name of the Controller and the Method (function) within
the Controller to execute. The following syntax works by default:</p>

<p>index.php?action=<em>controller</em>.<em>method</em></p>

<p>Phreeze uses a URLWriter to decide how URLs are written, so you can create friendly
URLs such as http://mysite.com/controller/method/ for example.  See _config.php.</p>

<h2>The View</h2>

<p>The View is the user interface.  These are the pages that your user will see
and use to interact with your application.</p>

<p>The default views that have been generated utilize the
<a href="http://smarty.php.net/">Smarty</a> template engine and 
<a href="http://www.extjs.com/">ExtJS</a> for UI widgets and controls.
</p>

{ldelim}include file="_footer.tpl"{rdelim}
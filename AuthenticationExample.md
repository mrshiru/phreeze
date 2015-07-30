# How to use Phreeze's Built-In Authentication #

Phreeze provides a built-in mechanism for authentication that you can use in your application.  This gives you the ability to require users to login, and restrict the actions that these users can perform.  You have two options for using this feature:

  1. Implement your own IAuthenticatable class
  1. Adhere to a compatible schema in your database

## Implement your own IAuthenticatable class ##

The base Controller class provides three methods that are used for authentication.  They are:

```
protected function SetCurrentUser(IAuthenticatable $user)
protected function GetCurrentUser()
protected function RequirePermission($permission, $on_fail_action = "")
```

If you create a class that implments the interface verysimple:Authentication:IAuthenticatable, you can pass it as an argument in any controller using SetCurrentUser().  This will store this user object in the session.  Once the current user is stored in the session, you simply can call RequirePermission() in any Controller.  The controller will make a call to $user->IsAuthorized($permission) to see if the current user has the permission specified.  If not, an error is displayed.

To require a permission for every method in a controller, you can add RequirePermission inside the Init() method.  Init() is fired just after construction, but before any action methods are called.  So, you can check for permission here like so:

```
protected function Init()
{
  $this->ModelName = "MyClass";
		
  $this->RequirePermission(P_READWRITE); // controller level authentication
}
```

This code would assume that you had defined a global variable P\_READWRITE which then was recognized by IAuthenticatable->IsAuthorized().  You can use integers or strings to represent permissions if you prefer, as long as IsAuthorized recognizes what it means.

## Authentication Using a Phreeze-Compatible Schema ##

The easiest way to implement authentication is by simply designing your database in a way that is compatible with Phreeze.  If you already have an existing schema that you can't change, it's still possible, but you may have to write a little bit of code.

This is the absolute minimum that your account/role tables must have.  You can have more columns than included here, but you have to have username, password and role\_id:

```
CREATE TABLE  `role` (
  `r_id` tinyint(3) unsigned NOT NULL default '0',
  `r_name` varchar(25) NOT NULL,
  `r_permission` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE  `account` (
  `a_id` int(10) unsigned NOT NULL auto_increment,
  `a_role_id` tinyint(3) unsigned NOT NULL,
  `a_username` varchar(50) NOT NULL,
  `a_password` varchar(250) NOT NULL,
  PRIMARY KEY  (`a_id`),
  KEY `a_role` (`a_role_id`),
  CONSTRAINT `a_role` FOREIGN KEY (`a_role_id`) REFERENCES `role` (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

```

If you create your authentication tables in such a way, you should wind up with two Model classes "Account" and "Role" and the Account class will have a method GetRole().

You need to make one change to the Account class.  Simply change the line:

```
class Account extends AccountDAO
```
to
```
class Account extends AuthAccount
```

Your Account will work almost exactly the same as before, however it has a couple of cool additions.  The first thing you will notice is that when you create or update an account, the password is now one-way crypted for security purposes.  You'll also get a new method Login which allows you to authenticate a user based on their username/password.

Here is an example of a method that would process a login request:

```
public function Authenticate()
{
  // create a new "AuthAccount"
  $account = new Account($this->Phreezer);

  if ( $account->Login(Request::Get("Username"),Request::Get("Password")))
  {
    // login success - save the user to the session context
    $this->SetCurrentUser($account);

    // TODO: send the user somewhere
    $this->Redirect("Default.ListAll");
  }
  else
  {
    // login fail - show the login form again w/ feedback
    $this->Redirect("Login.Form","The username/password combination was not found");
  }

}
```

One important thing that is not touched upon is the Role->Permission column and what should be entered here.  The Role table would normally contain entries like "admin", "visitor", "client" etc.  Each role can be assigned permissions.  Lets say that you define the following permission in your application:

```
define("P_READ",1);
define("P_WRITE",2);
define("P_UPDATE",4);
define("P_DELETE",8);
define("P_SUPERUSER",16);
```

If you are familiar with bitwise operations, the numbers will look very familiar to you.  If not, notice that each of the numbers assigned here is double the previous value: 1,2,4,8,16.  We skip various numbers.  This is critical because, when you use bitwise numbers like this we can perform more advanced calculations on them, which are particularly useful for authentication.

The Role class, you will notice has a Permission field which is an integer.  If you use bitwise integers as in the code above then here are some example values for Role->Permission:

  * role "admin" permission could be **31**  (16 + 8 + 4 + 2 + 1)
  * role "visitor" permission could be **1**
  * role "client" permission might be **7** (4 + 2 + 1)

If you are familiar with unix file permissions, this works on the same principle.   Basically you just take the value for each permission and add them all up.  That will give you a final number that goes in the Permission column for each role.  In the example above, the visitor only has one permission "1" which is "read" permission.  The client has 4,2,1 which is read,write,update permission.  The admin has all of the permission available.

Using the examples above, you can now check for permissions in your controller using bitwise operations like so:

```
// check if the current user has read permission
$this->RequirePermission(P_READ);

// check if the current user has read permission OR superuser permission
$this->RequirePermission(P_WRITE | P_SUPERUSER); 

// check if the current user has write permission AND delete permission
$this->RequirePermission(P_WRITE & P_DELETE); 
```

The AuthAccount class recognizes bitwise operations so you don't have to worry about any of the math.  You just need to make sure your permission id numbers use bitwise integers 1,2,4,8,16, etc.
<?php
/**
 * AccountMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the AccountDAO to the account datastore.
 *
 * This file is automatically generated by ClassBuilder.
 *
 * TODO: Review KeyMaps to determine if any should be loaded eagerly to improve performance
 *
 * @package    Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
 
require_once("verysimple/Phreeze/IDaoMap.php");

	
class AccountMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Account object
	 *
	 * @access static
	 * @return array of FieldMaps
	 */
	public static function GetFieldMaps()
	{
		static $fm = null;
		if ($fm == null)
		{
			$fm = Array();
			$fm["Id"] = new FieldMap("Id","account","a_id",true,FM_TYPE_INT,10,null,true);
			$fm["Status"] = new FieldMap("Status","account","a_status",false,FM_TYPE_TINYINT,4,"1",false);
			$fm["RoleId"] = new FieldMap("RoleId","account","a_role_id",false,FM_TYPE_TINYINT,4,null,false);
			$fm["FirstName"] = new FieldMap("FirstName","account","a_first_name",false,FM_TYPE_VARCHAR,25,null,false);
			$fm["LastName"] = new FieldMap("LastName","account","a_last_name",false,FM_TYPE_VARCHAR,25,null,false);
			$fm["Username"] = new FieldMap("Username","account","a_username",false,FM_TYPE_VARCHAR,75,null,false);
			$fm["Password"] = new FieldMap("Password","account","a_password",false,FM_TYPE_VARCHAR,80,null,false);
			$fm["Homepage"] = new FieldMap("Homepage","account","a_homepage",false,FM_TYPE_VARCHAR,75,null,false);
			$fm["Company"] = new FieldMap("Company","account","a_company",false,FM_TYPE_VARCHAR,100,null,false);
			$fm["Address1"] = new FieldMap("Address1","account","a_address1",false,FM_TYPE_VARCHAR,75,null,false);
			$fm["City"] = new FieldMap("City","account","a_city",false,FM_TYPE_VARCHAR,40,null,false);
			$fm["State"] = new FieldMap("State","account","a_state",false,FM_TYPE_VARCHAR,10,null,false);
			$fm["Zip"] = new FieldMap("Zip","account","a_zip",false,FM_TYPE_VARCHAR,10,null,false);
			$fm["Phone"] = new FieldMap("Phone","account","a_phone",false,FM_TYPE_VARCHAR,20,null,false);
			$fm["Fax"] = new FieldMap("Fax","account","a_fax",false,FM_TYPE_VARCHAR,20,null,false);
			$fm["Email"] = new FieldMap("Email","account","a_email",false,FM_TYPE_VARCHAR,50,null,false);
			$fm["TaxId"] = new FieldMap("TaxId","account","a_tax_id",false,FM_TYPE_VARCHAR,45,null,false);
			$fm["TaxFile"] = new FieldMap("TaxFile","account","a_tax_file",false,FM_TYPE_VARCHAR,40,null,false);
			$fm["Created"] = new FieldMap("Created","account","a_created",false,FM_TYPE_DATETIME,null,null,false);
			$fm["Modified"] = new FieldMap("Modified","account","a_modified",false,FM_TYPE_DATETIME,null,null,false);
		}
		return $fm;
	}
	
	/**
	 * Returns a singleton array of KeyMaps for the Account object
	 *
	 * @access static
	 * @return array of KeyMaps
	 */
	public static function GetKeyMaps()
	{
		static $km = null;
		if ($km == null)
		{
			$km = Array();
			$km["comments_assigned_from_account"] = new KeyMap("comments_assigned_from_account", "Id", "Comment", "AssignedFrom", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["comments_assigned_to_account"] = new KeyMap("comments_assigned_to_account", "Id", "Comment", "AssignedTo", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["comments_created_by_account"] = new KeyMap("comments_created_by_account", "Id", "Comment", "CreatedBy", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["tickets_assigned_to_account"] = new KeyMap("tickets_assigned_to_account", "Id", "Ticket", "AssignedTo", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["tickets_submitted_by_account"] = new KeyMap("tickets_submitted_by_account", "Id", "Ticket", "SubmittedBy", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["units_account"] = new KeyMap("units_account", "Id", "Unit", "OwnerId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);
			$km["accounts_role"] = new KeyMap("accounts_role", "RoleId", "Role", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // change to KM_LOAD_EAGER to outer-join this table on every query
		}
		return $km;
	}
	
}

?>
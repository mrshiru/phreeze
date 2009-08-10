	// include libraries used in dynamic class creation
{foreach from=$tableInfos item=tableInfo}
	import com.pcguild.mtdclient.vo.{$tableInfo.singular}VO;
	import com.pcguild.mtdclient.business.{$tableInfo.singular}DAODelegate;
	import com.pcguild.mtdclient.events.{$tableInfo.singular|lower}.{$tableInfo.singular}DeleteEvent;
	import com.pcguild.mtdclient.events.{$tableInfo.singular|lower}.{$tableInfo.singular}GetEvent;
	import com.pcguild.mtdclient.events.{$tableInfo.singular|lower}.{$tableInfo.singular}InsertEvent;
	import com.pcguild.mtdclient.events.{$tableInfo.singular|lower}.{$tableInfo.singular}UpdateEvent;
{/foreach}

	// create an instance of each object that may be required in dynamic class creation
{foreach from=$tableInfos item=tableInfo}
		private var null{$tableInfo.singular}VO:{$tableInfo.singular}VO;
		private var null{$tableInfo.singular}Delegate:{$tableInfo.singular}DAODelegate;
		private var null{$tableInfo.singular}GetEvent:{$tableInfo.singular}GetEvent;
		private var null{$tableInfo.singular}InsertEvent:{$tableInfo.singular}InsertEvent;
		private var null{$tableInfo.singular}UpdateEvent:{$tableInfo.singular}UpdateEvent;
		private var null{$tableInfo.singular}DeleteEvent:{$tableInfo.singular}DeleteEvent;
{/foreach}
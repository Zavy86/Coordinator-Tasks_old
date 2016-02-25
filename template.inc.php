<?php
/* -------------------------------------------------------------------------- *\
|* -[ Tasks - Template ]----------------------------------------------------- *|
\* -------------------------------------------------------------------------- */
include("module.inc.php");
include("../core/api.inc.php");
api_loadModule();
// print header
$html->header(api_text("module-title"),$module_name);
// build navigation tab
global $navigation;
$navigation=new str_navigation((api_baseName()=="tasks_list.php"?TRUE:FALSE));
// list
$navigation->addTab(api_text("nav-list"),"tasks_list.php");
// filters
if(api_baseName()=="tasks_list.php"){
 // export
 $navigation->addSubTab(api_text("nav-export"),"tasks_export.php",NULL,NULL,TRUE,"_blank");
 // status
 $navigation->addFilter("multiselect","status",api_text("filter-status"),array(1=>api_text("task-status-inserted"),2=>api_text("task-status-processing"),3=>api_text("task-status-completed"))); //,4=>api_text("task-status-archived"
 if(api_checkPermission($module_name,"tasks_view_all")){
  // referent
  $filter_account_array=array();
  $contacts=$GLOBALS['db']->query("SELECT accounts_accounts.id,accounts_accounts.name FROM tasks_tasks JOIN accounts_accounts ON accounts_accounts.id=tasks_tasks.idAccount ORDER BY accounts_accounts.name ASC");
  while($contact=$GLOBALS['db']->fetchNextObject($contacts)){$filter_account_array[$contact->id]=stripslashes($contact->name);}
  $navigation->addFilter("multiselect","idAccount",api_text("filter-account"),$filter_account_array);
 }
 // if not filtered load default filters
 if($_GET['resetFilters']||($_GET['filtered']<>1&&$_SESSION['filters'][api_baseModule()][api_baseName()]['filtered']<>1)){$_GET['status']=array(1,2);}
}
// add or edit
if(api_baseName()=="tasks_edit.php" && $_GET['idTask']){
 $navigation->addTab(api_text("nav-edit"),"tasks_edit.php");
}else{
 $navigation->addTab(api_text("nav-add"),"tasks_edit.php");
}
// show navigation
$navigation->render();
// check permissions before displaying module
if($checkPermission==NULL){content();}else{if(api_checkPermission($module_name,$checkPermission,TRUE)){content();}}
// print footer
$html->footer();
?>
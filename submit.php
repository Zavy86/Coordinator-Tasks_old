<?php
/* -------------------------------------------------------------------------- *\
|* -[ Tasks - Submit ]-------------------------------------------------------- *|
\* -------------------------------------------------------------------------- */
include('../core/api.inc.php');
include('api.inc.php');
$act=$_GET['act'];
switch($act){
 // letterheads
 case "task_save":task_save();break;
 case "task_status":task_status();break;
 case "task_delete":task_delete();break;
 // default
 default:
  $alert="?alert=submitFunctionNotFound&alert_class=alert-warning&act=".$act;
  exit(header("location: index.php".$alert));
}


/**
 * Task Save
 */
function task_save(){
 if(!api_checkPermission("tasks","tasks_usage")){api_die("accessDenied");}
 // get objects
 $task=api_tasks_task($_GET['idTask']);
 // acquire variables
 $p_idAccount=$_POST['idAccount'];
 $p_subject=addslashes(api_cleanString($_POST['subject'],"/[^A-Za-zÀ-ÿ0-9-_' ]/"));
 $p_description=addslashes($_POST['description']);
 // check variables
 if(!$p_idAccount){$p_idAccount=api_account()->id;}
 // check permissions
 if($p_idAccount<>api_account()->id && !api_checkPermission("tasks","tasks_edit_all")){api_die("accessDenied");}
 // build query
 if($task->id){
  $query="UPDATE `tasks_tasks` SET
   `idAccount`='".$p_idAccount."',
   `subject`='".$p_subject."',
   `description`='".$p_description."',
   `updDate`='".api_now()."',
   `updIdAccount`='".api_account()->id."'
   WHERE `id`='".$task->id."'";
  // execute query
  $GLOBALS['db']->execute($query);
  // log event
  $log=api_log(API_LOG_NOTICE,"tasks","taskUpdated",
   "{logs_tasks_taskUpdated|".$task->id."|".$p_subject."|".api_account($p_idAccount)->name."}",
   $task->id,"tasks/tasks_list.php?idTask=".$task->id);
  // redirect
  $alert="&alert=taskUpdated&alert_class=alert-success&idLog=".$log->id;
  exit(header("location: tasks_list.php?idTask=".$task->id.$alert));
 }else{
  $query="INSERT INTO `tasks_tasks`
   (`idAccount`,`subject`,`description`,`status`,`addDate`,`addIdAccount`,`updDate`,`updIdAccount`) VALUES
   ('".$p_idAccount."','".$p_subject."','".$p_description."','1',
    '".api_now()."','".api_account()->id."','".api_now()."','".api_account()->id."')";
  // execute query
  $GLOBALS['db']->execute($query);
  // get last inserted id
  $q_idTask=$GLOBALS['db']->lastInsertedId();
  // log event
  $log=api_log(API_LOG_NOTICE,"tasks","taskCreated",
   "{logs_tasks_taskCreated|".$q_idTask."|".$p_subject."|".api_account($p_idAccount)->name."}",
   $q_idTask,"tasks/tasks_list.php?idTask=".$q_idTask);
  // redirect
  $alert="&alert=taskCreated&alert_class=alert-success&idLog=".$log->id;
  exit(header("location: tasks_list.php?idTask=".$q_idTask.$alert));
 }
}

/**
 * Task Status
 */
function task_status(){
 if(!api_checkPermission("tasks","tasks_usage")){api_die("accessDenied");}
 // get objects
 $task=api_tasks_task($_GET['idTask']);
 // acquire variables
 $g_status=$_GET['status'];
 // check variables
 if($g_status<1||$g_status>3){$g_status=1;}
 // check objects
 if(!$task->id){exit(header("location: tasks_list.php?alert=taskNotFound&alert_class=alert-error"));}
 // check permissions
 if($task->idAccount<>api_account()->id && !api_checkPermission("tasks","tasks_edit_all")){api_die("accessDenied");}
 // build query
 $query="UPDATE `tasks_tasks` SET `status`='".$g_status."' WHERE `id`='".$task->id."'";
 // execute query
 $GLOBALS['db']->execute($query);
 // log event
 $log=api_log(API_LOG_NOTICE,"tasks","taskStatus".$g_status,
  "{logs_tasks_taskStatus".$g_status."|".$task->id."|".$task->subject."|".api_account($task->idAccount)->name."}",
  $task->id,"tasks/tasks_list.php?idTask=".$task->id);
 // redirect
 $alert="&alert=taskStatus".$g_status."&alert_class=alert-success&alert_parameters=".$task->subject."&idLog=".$log->id;
 exit(header("location: tasks_list.php?idTask=".$task->id.$alert));
}

/**
 * Task Delete
 */
function task_delete(){
 if(!api_checkPermission("tasks","tasks_usage")){api_die("accessDenied");}
 // get objects
 $task=api_tasks_task($_GET['idTask']);
 // check objects
 if(!$task->id){exit(header("location: tasks_list.php?alert=taskNotFound&alert_class=alert-error"));}
 // check permissions
 if($task->idAccount<>api_account()->id && !api_checkPermission("tasks","tasks_edit_all")){api_die("accessDenied");}
 // build query
 $query="DELETE FROM `tasks_tasks` WHERE `id`='".$task->id."'";
 // execute query
 $GLOBALS['db']->execute($query);
 // log event
 $log=api_log(API_LOG_WARNING,"tasks","taskDeleted","{logs_tasks_taskDeleted|".$task->id."|".$task->subject."|".api_account($task->idAccount)->name."}",$task->id);
 // redirect
 $alert="?alert=taskDeleted&alert_class=alert-warning&alert_parameters=".$task->subject."&idLog=".$log->id;
 exit(header("location: tasks_list.php".$alert));
}

?>
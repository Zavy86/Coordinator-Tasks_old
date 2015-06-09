<?php
/* -------------------------------------------------------------------------- *\
|* -[ Tasks - Tasks List ]---------------------------------------------------- *|
\* -------------------------------------------------------------------------- */
$checkPermission="tasks_usage";
include("template.inc.php");
function content(){
 // definitions
 $modals_array=array();
 $status_modals_array=array();
 // acquire variables
 $g_search=$_GET['q'];
 // show filters
 echo $GLOBALS['navigation']->filtersText();
 // build table
 $table=new str_table(api_text("tasks_list-tr-unvalued"),TRUE,$GLOBALS['navigation']->filtersGet());
 $table->addHeader("&nbsp;",NULL,"16");
 if(count($_GET['idAccount'])){$table->addHeader(api_text("tasks_list-th-account"),"nowarp",NULL,"accounts_accounts.name");}
 $table->addHeader(api_text("tasks_list-th-subject"),NULL,"100%","tasks_tasks.subject");
 $table->addHeader(api_text("tasks_list-th-updDate"),"nowarp text-right",NULL,"tasks_tasks.updDate");
 $table->addHeader(api_text("tasks_list-th-status"),"nowarp",NULL,"tasks_tasks.status");
 // get signatures
 $tasks=api_tasks_tasks($g_search,TRUE);
 foreach($tasks->results as $task){
  $tr_class=NULL;
  // build modal window
  $modals_array[]=api_tasks_taskModal($task);
  $status_modals_array[]=api_tasks_taskStatusModal($task);
  // check selected
  if($task->id==$_GET['idTask']){$tr_class="info";}
  // build group table row
  $table->addRow($tr_class);
  // build table fields
  $table->addField(end($modals_array)->link(api_tasks_taskStatusText($task,FALSE,TRUE)),"nowarp");
  if(count($_GET['idAccount'])){$table->addField($task->accountName,"nowarp");}
  $table->addField($task->subject);
  $table->addField(api_timestampFormat($task->updDate,api_text("datetime")),"nowarp text-right");
  $table->addField(end($status_modals_array)->link($task->statusText,"hiddenlink"),"nowarp");
 }
 // renderize table
 $table->render();
 // renderize the pagination
 $tasks->pagination->render();
 // renderize status modal windows
 foreach($modals_array as $modal){$modal->render();}
 foreach($status_modals_array as $status_modal){$status_modal->render();}
 // store to session for export
 $_SESSION['tasks']['export']=$tasks->results;
 // debug
 if($_SESSION["account"]->debug){
  pre_var_dump($tasks->query,"print","query");
  pre_var_dump($tasks->results,"print","tasks");
 }
}
?>
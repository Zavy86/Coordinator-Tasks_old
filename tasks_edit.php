<?php
/* -------------------------------------------------------------------------- *\
|* -[ Tasks - Tasks Edit ]---------------------------------------------------- *|
\* -------------------------------------------------------------------------- */
$checkPermission="tasks_usage";
include("template.inc.php");
function content(){
 // get objects
 $task=api_tasks_task($_GET['idTask']);
 // build form
 $form=new str_form("submit.php?act=task_save&idTask=".$task->id,"post","tasks_edit");
 if(api_checkPermission("tasks","tasks_edit_all")){
  if(!$GLOBALS['db']->countOf("accounts_accounts","enabled='1'")>10){
   $form->addField("hidden","idAccount",api_text("tasks_edit-ff-account"),$task->idAccount,"input-xlarge",api_account()->name);
  }else{
   $form->addField("select","idAccount",api_text("tasks_edit-ff-account"),NULL,"input-xlarge");
   $accounts=$GLOBALS['db']->query("SELECT * FROM accounts_accounts WHERE enabled='1' ORDER BY name ASC");
   $form->addFieldOption(api_account()->id,api_account()->name);
   while($account=$GLOBALS['db']->fetchNextObject($accounts)){$form->addFieldOption($account->id,stripslashes($account->name),($account->id==$task->idAccount?TRUE:FALSE));}
  }
 }else{
  $form->addField("hidden","idAccount",NULL,api_account()->id);
 }
 $form->addField("text","subject",api_text("tasks_edit-ff-subject"),$task->subject,"input-xxlarge");
 $form->addField("textarea","description",api_text("tasks_edit-ff-description"),$task->description,"input-xxlarge");
 // controls
 $form->addControl("submit",api_text("tasks_edit-fc-submit"));
 $form->addControl("button",api_text("tasks_edit-fc-cancel"),NULL,"tasks_list.php?idTask=".$task->id);
 if($task->id){$form->addControl("button",api_text("tasks_edit-fc-delete"),"btn-danger","submit.php?act=task_delete&idTask=".$task->id,api_text("tasks_edit-fc-delete-confirm"));}
 // renderize form
 $form->render();
 // debug
 if($_SESSION["account"]->debug){pre_var_dump($task,"print","task");}
?>
<script type="text/javascript">
 $(document).ready(function(){
  // validation
  $("form[name='tasks_edit']").validate({
   rules:{
    idAccount:{required:true},
    subject:{required:true}
   },
   submitHandler:function(form){form.submit();}
  });
<?php if(api_checkPermission("tasks","tasks_edit_all")){ ?>
  // select2 idAccount
  $("input[name=idAccount]").select2({
   placeholder:"<?php echo api_account()->name; ?>",
   minimumInputLength:2,
   allowClear:true,
   ajax:{
    url:"../accounts/accounts_json.inc.php",
    dataType:"json",
    data:function(term,page){return{q:term};},
    results:function(data,page){return{results:data};}
   },
   initSelection:function(element,callback){
    var id=$(element).val();
    if(id!==""){
     $.ajax("../accounts/accounts_json.inc.php?q="+id,{
      dataType:"json"
     }).done(function(data){callback(data[0]);});
    }
   }
  });
<?php } ?>
 });
</script>
<?php } ?>
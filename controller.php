<?php 
include "functions.php";
$HTTP_HOST = 'localhost';
$filefolder = './track/';
$content  = "";
$action = $_REQUEST['action'];
if(isset($_REQUEST['folder']))
    $folder = $_REQUEST['folder'];
else 
    $folder = "";

if(isset($_REQUEST['prevfolder']))
    $prevfolder = $_REQUEST['prevfolder'];
else 
    $prevfolder = "";
    
while (preg_match('/\.\.\//',$folder)) $folder = preg_replace('/\.\.\//','/',$folder);
while (preg_match('/\/\//',$folder)) $folder = preg_replace('/\/\//','/',$folder);

if ($folder == '') {
  $folder = $filefolder;
} elseif ($filefolder != '') {
  if (!preg_match('/\.\//',$folder)) {
    $folder = $filefolder;
  }  
}
        
switch($action) {
    case "list":
    directorylist($folder);
    break;
    case "upload":
	$serverResp = upload($_FILES['upfile'], $_REQUEST['ndir']);
	header("location:upload.php?folder=".$_REQUEST['ndir']."&msgtype=".$serverResp['msgType']."&msg=".$serverResp['msg']);
	break;

    case "delete":
	delete($_REQUEST['dename']);
	break;

    case "create":
	create($_REQUEST['nfname'], $_REQUEST['ndir']);
	break;

    case "rename":
	renam($_REQUEST['file'], $_REQUEST['newname'], $folder);
	break;
	
	case "move":
	move($_REQUEST['file'], $_REQUEST['newdir'], $folder);
	break;
	
	case "multimove":
	multimove($_REQUEST['multicheckbox'], $_REQUEST['newdir'], $folder);
	break;
	
	case "multidelete":
	multidelete($_REQUEST['multicheckbox']);
	break;
	
	case "download":
	download($folder.$_REQUEST['file']);
	break;

    default:
	directorylist($folder);
	break;
}
die();
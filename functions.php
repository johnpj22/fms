<?php 
function directorylist($folder) {
    global $filefolder, $HTTP_HOST, $prevfolder, $content;
    
    $content1 = "";
    $content2 = "";
    $adminfile = "controller.php";
    $count = "0";
    $style = opendir($folder);
    $a=1;
    $b=1;
    $c=0;
    if ($folder) {
        if (preg_match("/home/",$folder)) {
            $folderx = preg_replace("$filefolder", "", $folder);
            $folderx = "http://".$HTTP_HOST."/".$folderx;
        } else {
            $folderx = $folder;
        }
    }
    $htmlcontent1='';
    $htmlcontent2='';
    while($stylesheet = readdir($style)) {
        $sstylesheet = $stylesheet;
        if ($stylesheet[0] != "." && $stylesheet[0] != ".." ) {
            if (is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) {
                $content1[$a]['folder'] = $folder;
                $content1[$a]['name'] = $stylesheet;
                $htmlcontent1 .= "<tr><td><input type=\"checkbox\" class=\"multicheckbox\" name=\"multicheckbox[]\"  value=\"".$stylesheet."\" ></td>"
            			."<td>".$sstylesheet."</td>"
                		."<td>Dir</td>"
                        ."<td ><a onclick=\"return doAction('open',this);\" href=\"".$adminfile."?action=list&prevfolder=".$folder."&folder=".$folder.$stylesheet."/\">Open</a></td>"
                        ."<td ><a class=\"btn btn-primary\" onclick=\"return doAction('rename',this);\" href=\"".$adminfile."?action=rename&file=".$stylesheet."&folder=$folder\"  data-name=\"".$stylesheet."\" >Rename</a>&nbsp;"
                        ."<a class=\"btn btn-danger\" onclick=\"return doAction('delete',this);\" href=\"".$adminfile."?action=delete&dename=".$stylesheet."&folder=$folder\">Delete</a>&nbsp;"
                        ."<a class=\"btn btn-warning\" onclick=\"return doAction('move',this);\" href=\"".$adminfile."?action=move&file=".$stylesheet."&folder=$folder\" data-name=\"".$folder.$stylesheet."\" >Move</a></td></tr>";
                
                $a++;
            } elseif (!is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) {
                $content2[$b]['folder'] = $folder;
                $content3[$b]['name'] = $stylesheet; 
                $htmlcontent2 .="<tr><td><input type=\"checkbox\" class=\"multicheckbox\" name=\"multicheckbox[]\" value=\"".$stylesheet."\" ></td>"
                		."<td>".$sstylesheet."</td>"
                		."<td></td>"
                        ."<td>".filesize($folder.$stylesheet).' Bytes</td>'
                        ."<td ><a class=\"btn btn-primary\" onclick=\"return doAction('rename',this);\" href=\"".$adminfile."?action=rename&file=".$stylesheet."&folder=$folder\" data-name=\"".$stylesheet."\">Rename</a>&nbsp;"
                        ."<a class=\"btn btn-danger\" onclick=\"return doAction('delete',this);\" href=\"".$adminfile."?action=delete&dename=".$stylesheet."&folder=$folder\">Delete</a>&nbsp;"
                        ."<a class=\"btn btn-warning\" onclick=\"return doAction('move',this);\" href=\"".$adminfile."?action=move&file=".$stylesheet."&folder=$folder\" data-name=\"".$folder.$stylesheet."\">Move</a>&nbsp;"
                        ."<a class=\"btn btn-success\" href=\"".$adminfile."?action=download&file=".$stylesheet."&folder=$folder\">Download</a></td></tr>";
                $b++;
            } else {
                $c++;
            }
            $count++;
        }
    }
    closedir($style);
    $response['total'] = $count;
    $response['folder']['count'] = count($content1);
    $response['folder']['list'] = $content1;
    $response['file']['count'] = count($content2);
    $response['file']['list'] = $content2;
    $response['content'] = $htmlcontent1.''.$htmlcontent2;
    if($count == "0")
    	$response['content'] = "<tr><td align=\"center\" colspan=\"6\"><b>No Data Found</b></td></tr>";
    	
    $response['activedirectory'] = $folder;
    if($folder != $filefolder)
    	$response['currentdirectory'] = $folder." &nbsp;&nbsp; <a onclick=\"return doAction('open',this);\" href=\"".$adminfile."?action=list&folder=".$prevfolder."\">&lt;&lt;Move Up</a>";
    else 
    	$response['currentdirectory'] = $folder;
    $content = "<option value=\"".$filefolder."\">".$filefolder."</option>";	
    listdir($filefolder);	
    $response['listdirectory'] = $content;
	echo json_encode($response);
}

/****************************************************************/
/* function upload()                                            */
/*                                                              */
/* Sencond step in upload.                                      */
/* Saves the file to the disk.                                  */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function upload($upfile, $ndir) {

    global $folder;
    if (!$upfile) {
        $response['msg'] = "File failed to upload.";
        $response['msgType'] = "error";
    } elseif($upfile['name']) {
        if(copy($upfile['tmp_name'],$ndir.$upfile['name'])) {
            $response['msg'] = "file uploaded successfully.";
            $response['msgType'] = "success";
        } else {
            $response['msg'] = "File failed to upload.";
        $response['msgType'] = "error";
        }
    } else {
        $response['msg'] = "File failed to upload.";
        $response['msgType'] = "error";
    }
    return $response;
}

/****************************************************************/
/* function delete()                                            */
/*                                                              */
/* Second step in delete.                                       */
/* Deletes the actual file from disk.                           */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function delete($dename) {
    global $folder;
    if (!$dename == "") {
        if (is_dir($folder.$dename)) {
            
            $dir= $folder.$dename;
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            if(rmdir($dir)) {
                $response['msg'] = $dename." has been deleted.";
                $response['msgType'] = "success";
            } else {
                $response['msg'] = "There was a problem deleting this directory. ";
                $response['msgType'] = "error";
            }
        } else {
            if(unlink($folder.$dename)) {
                $response['msg'] = $dename." has been deleted.";
                $response['msgType'] = "success";
            } else {
                $response['msg'] = "There was a problem deleting this file. ";
                $response['msgType'] = "error";
            }
        }
    } else {
        $response['msg'] = "There was a problem deleting this file. ";
        $response['msgType'] = "error";
    }
    echo json_encode($response);
}

/****************************************************************/
/* function create()                                            */
/*                                                              */
/* Second step in create.                                       */
/* Creates the file/directoy on disk.                           */
/* Recieves $nfname from cr() as the filename.                  */
/* Recieves $infolder from cr() to determine file trpe.         */
/****************************************************************/
function create($nfname, $ndir) {
    global $folder;
    $isfolder = 1;
    $ndir = trim($ndir,"/");
    if (!$nfname == "") {
        if ($isfolder == 1) {
            if(mkdir($ndir."/".$nfname, 0777)) {
                $response['msg'] = $ndir."/".$nfname." succesfully created";
                $response['msgType'] = "success";
            } else {
                $response['msg'] = "The directory, ".$ndir."/".$nfname." could not be created. Check to make sure the permisions on the /files directory is set to 777";
                $response['msgType'] = "error";
            }
        } else {
            if(fopen($ndir."/".$nfname, "w")) {
                $response['msg'] = "Your file, ".$ndir."/".$nfname." succesfully created";
                $response['msgType'] = "success";
            } else {
                $response['msg'] = "The file, ".$ndir."/".$nfname." could not be created. Check to make sure the permisions on the /files directory is set to 777";
                $response['msgType'] = "error";
            }
        }
    } else {
        $response['msg'] = "There is a problem";
        $response['msgType'] = "error";
    }
    echo json_encode($response);
}


/****************************************************************/
/* function renam()                                             */
/*                                                              */
/* Second step in rename.                                       */
/* Rename the specified file.                                   */
/* Recieves $rename from ren() as the old  filename.            */
/* Recieves $nrename from ren() as the new filename.            */
/****************************************************************/
function renam($rename, $newname, $folder) {
    global $folder;
    if (!$rename == "") {
        $loc1 = "$folder".$rename;
        $loc2 = "$folder".$newname;

        if(rename($loc1,$loc2)) {
            $response['msg'] = "Renamed succesfully";
            $response['msgType'] = "success";
        } else {
            $response['msg'] = "There is a problem";
            $response['msgType'] = "error";
        }        
    } else {
        $response['msg'] = "There is a problem";
        $response['msgType'] = "error";
    }
    echo json_encode($response);
}

/****************************************************************/
/* function listdir()                                           */
/*                                                              */
/* Recursivly lists directories and sub-directories.            */
/* Recieves $dir as the directory to scan through.              */
/****************************************************************/
function listdir($dir, $level_count = 0) {
    global $content;
    if (!@($thisdir = opendir($dir))) { return; }
    while ($item = readdir($thisdir) ) {
        if (is_dir("$dir/$item") && (substr("$item", 0, 1) != '.')) {
            listdir("$dir/$item", $level_count + 1);
        }
    }
    if ($level_count > 0) {
        $dir = str_replace("//", "/", $dir);
        $content .= "<option value=\"".$dir."/\">".$dir."/</option>";
    }
}


/****************************************************************/
/* function move()                                              */
/*                                                              */
/* Second step in move.                                         */
/* Moves the oldfile to the new one.                            */
/* Recieves $file and $ndir and creates $file.$ndir             */
/****************************************************************/
function move($file, $newdir, $folder) {
    global $folder;
    if (!$file == "") {
        if (rename($folder.$file, $newdir.$file)) {
            $response['msg'] = $folder.$file." has been succesfully moved to ".$newdir.$file;
            $response['msgType'] = "success";
        } else {
            $response['msg'] = "There is a problem";
            $response['msgType'] = "error";
        }        
    } else {
        $response['msg'] = "There is a problem";
        $response['msgType'] = "error";
    }
    echo json_encode($response);
}

function multimove($files, $newdir, $folder) {
    global $folder;
    foreach($files as $file){
        if (!$file == "") {
            if (rename($folder.$file, $newdir.$file)) {
                $response['msg'] = "Files has been succesfully moved to ".$newdir.$file;
                $response['msgType'] = "success";
            } else {
                $response['msg'] = "There is a problem";
                $response['msgType'] = "error";
            }        
        } else {
            $response['msg'] = "There is a problem";
            $response['msgType'] = "error";
        }
    }
    echo json_encode($response);
}

function multidelete($denames) {
    global $folder;
    foreach($denames as $dename){
        if (!$dename == "") {
            if (is_dir($folder.$dename)) {
                
                $dir= $folder.$dename;
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
                foreach($files as $file) {
                    if ($file->isDir()){
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                if(rmdir($dir)) {
                    $response['msg'] = "Files has been deleted.";
                    $response['msgType'] = "success";
                } else {
                    $response['msg'] = "There was a problem deleting this directory. ";
                    $response['msgType'] = "error";
                }
            } else {
                if(unlink($folder.$dename)) {
                    $response['msg'] = "Files has been deleted.";
                    $response['msgType'] = "success";
                } else {
                    $response['msg'] = "There was a problem deleting this file. ";
                    $response['msgType'] = "error";
                }
            }
        } else {
            $response['msg'] = "There was a problem deleting this file. ";
            $response['msgType'] = "error";
        }
    }
    echo json_encode($response);
}


function download($file) {
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}
?>
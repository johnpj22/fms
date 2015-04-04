<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>File Management</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <form action="controller.php" id="formupload" method="post" enctype="multipart/form-data" >
        <div class="form-group"><label for="upfile-name"
        	class="control-label">File:</label> <input type="file"
        	class="form-control" id="upfile-name" name="upfile"></div>
        	<input type="hidden"
        	class="form-control" name="action" value="upload">
        	<input type="hidden"
        	class="form-control" name="ndir" value="<?php echo $_GET['folder']?>">						
    </form>
</body><?php if(isset($_GET['msg']) && isset($_GET['msgtype'])){?>
<script>window.parent.formSubmitResponse({'msg':"<?php echo $_GET['msg']?>",'msgType':"<?php echo $_GET['msgtype']?>"})</script>
<?php }?>
</html>

<?php

date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');

//$date        = date('Y-m-d');
$ip_add      = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;

// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken'])? $_GET['dltoken']:NULL;
if(!empty($dltoken)){

$dele = $obj->Delete_data("clients","clients_id='$dltoken'");

if(!$dele)
    {$notification = 'Delete Successfull';}
else
    {$notification = 'Delete Failed';} 
}
// ========== Delete Function End =================




if(isset($_POST['submit'])){

//Start Random code Generator
$seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
$rand1 = array_rand($seed, 6);
$convert1 = array_map(function($n){
    global $seed;
    return $seed[$n];
},$rand1);

$character1 = implode('',$convert1);

$seed = str_split('1234567890');
$rand1 = array_rand($seed, 4);

$convert1 = array_map(function($n){
    global $seed;
    return $seed[$n];
},$rand1);


$digit1 = implode('',$convert1);

$rend_code1 = "BSTL" . "$character1" . "$digit1";

//End Random code Generator


$nep1 = "asset/clients/" . $rend_code1 . ".jpg";

if ($_FILES["co_logo"]["name"]){
    if (copy($_FILES["co_logo"]["tmp_name"], $nep1)){
    $co_logo_path = "asset/clients/" . $rend_code1 . ".jpg";
    }    
}
else
    {$co_logo_path = '';}


    extract($_POST);
    $form_data = array(
      'LanguageId' => $LanguageId,
      'caption_title' => $caption_title,
      'co_web_url' => $web_url,
      'co_logo_path' => $co_logo_path,
      'status' => $status,
      'EntryBy' => $userid,       
      'EntryDate' => $date_time,
      'UpdateBy' => $userid
      );
    $created_id = $obj->Insert_data("clients", $form_data);

        if($created_id)
          {$notification = 'Saved Successfull';}
        else
          {$notification = 'Saved Failed';}
}

?>

<form role="form" enctype="multipart/form-data" method="post"> 

<div class="col-md-12" style=" background-image:url(asset/img/content_h1.png); margin-top:20px; margin-bottom: 15px; min-height:40px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <b>Clients Information</b>
</div>
<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
<b><?php echo isset($notification)? $notification :NULL; ?></b>
</div>
<div class="row">


<div class="col-md-7">
<div class="form-group">
       <label>Language Name </label>
       <select class="form-control" required="required" name="LanguageId" id="LanguageId" >
          <option value="">Select Language</option>
          <?php
          foreach ($obj->view_all("_languages") as $value){
          extract($value);
          ?>
          <option value="<?php echo isset($value['LanguageId'])? $value['LanguageId']:NULL; ?>"><?php echo isset($value['LanguageName'])? $value['LanguageName']:NULL; ?></option>       
          <?php } ?>    
      </select>
  </div>

   <div class="form-group" >
      <label>Caption Title</label>
      <textarea cols="80" id="editor1" name="caption_title" rows="10"></textarea>
   </div> 

   <div class="form-group" >
      <label>Web URL</label>
      <input type="text" name="web_url" class="form-control" id="DownloadFileName" placeholder="Name of Person" required>
   </div>

   <div class="form-group">
      <img width="140" height="140" src="asset/img/def_img.png" alt="..." class="img-thumbnail" id="pre_photo">
   </div>

   <div class="form-group">
    <label>Company Logo (127X74)xp</label>
    <input type="file" onchange="usershow_photo(this)" id="photo" name="co_logo" required>
   </div>

<div class="form-group" >
  <label>Status</label>
   <select class="form-control" name="status" id="LanguageId" required>
      <option value="">- - -</option> 
      <option value="1">Active</option>    
      <option value="0">InActive</option>
  </select>
</div>

<div class="form-group" >
  <button type="submit" class="btn btn-success" name="submit">Submit</button> 
</div>

</div>

     
</div>

</form>




<div class="col-md-12" style=" background-image:url(asset/img/content_h1.png); margin-top:20px; margin-bottom: 15px; min-height:40px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
<b>View Client Information List </b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">

    <div class="col-md-12">

        <table class="table table-condensed">
            <tr>
                <th style="width: 20px;">#</th>
                <th>Language Name</th>
                <th>Caption Title</th>
                <th>Web URL</th>
                <th>Co. Logo</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

               <?php
                $i = '0';
                foreach ($obj->view_all("vw_client") as $value){
                extract($value);
                $i++;
                ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo isset($value['LanguageName'])? $value['LanguageName']:NULL; ?></td>
                <td><?php echo isset($value['caption_title'])? $value['caption_title']:NULL; ?></td>
                <td><?php echo isset($value['co_web_url'])? $value['co_web_url']:NULL; ?></td>
                <td><img src="<?php echo isset($value['co_logo_path'])? $value['co_logo_path'] :NULL; ?>" width="40px" height="40px"></td>
                <td><?php if($value['status']=='1'){echo 'Active';} else{echo 'InAxtive';} ?></td>
                <td>
                    <div class="btn-group">
                        <?php foreach ($acc as $per){if($per=='edit'){ ?>
                        <a class="btn btn-xs btn-info" href="?q=edit_clients&token=<?php echo isset($value['clients_id'])? $value['clients_id'] :NULL; ?>">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <?php 
                        }} 
                        foreach ($acc as $per){if($per=='delete'){
                        ?>
                        <a class="btn btn-xs btn-danger" style="margin-left: 1px; margin-top: 5px;" href="?q=clients&dltoken=<?php echo isset($value['clients_id'])? $value['clients_id'] :NULL; ?>">
                             <span class="glyphicon glyphicon-remove"></span>
                        </a> 
                        <?php }} ?>                                                                                                                                   
                    </div>   
                </td>                
            </tr>
            
            <?php
              }
            ?>
       
        </table>
    </div>
</div>



<script>
  CKEDITOR.replace( 'editor1' );
</script>
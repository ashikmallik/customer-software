<?php
date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add      = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;
$token=isset ($_GET['token'])?$_GET['token']:NULL;

$details=$obj->details_by_cond("vw_client","clients_id='$token'");
extract($details);

if(isset ($_POST['update'])){
  extract($_POST);

$seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
$rand = array_rand($seed, 6);
$convert = array_map(function($n){
    global $seed;
    return $seed[$n];
},$rand);
$character = implode('',$convert);

$seed = str_split('1234567890');
$rand = array_rand($seed, 4);
$convert = array_map(function($n){
    global $seed;
    return $seed[$n];
},$rand);
$digit = implode('',$convert);

$rend_code1 = "BSTL" . "$character" . "$digit";

$nep1 = "asset/clients/" . $rend_code1 . ".jpg";

if (!empty($_FILES["co_logo"]["name"])){
    if (copy($_FILES["co_logo"]["tmp_name"], $nep1)){
    $co_logo_path = "asset/clients/" . $rend_code1 . ".jpg";
    }    
}
else
    {$co_logo_path = isset($details['co_logo_path'])? $details['co_logo_path']:NULL;}




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
    $clients_id=$obj->Update_data("clients",$form_data,"where clients_id='$token'");
    
    
     if($clients_id){
         ?>
<script>

window.location="?q=clients";

</script>

<?php
         
     }
     else{
           echo $notification = 'Update Failed';
     }
         
   }

?>



<form role="form" enctype="multipart/form-data" method="post"> 

<div class="col-md-12" style=" background-image:url(asset/img/content_h1.png); margin-top:20px; margin-bottom: 15px; min-height:40px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <b>Edit Clients Information</b>
</div>
<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
<b><?php echo isset($notification)? $notification :NULL; ?></b>
</div>
<div class="row">
    <div class="col-md-7">
<div class="form-group">
    <label >Language Name </label>
       <select class="form-control" required="required" name="LanguageId" id="LanguageId">
          <option value="">Select Language</option>
          <?php
              foreach ($obj->view_all("_languages") as $value){
              extract($value);
          ?>
          <option <?php if($details['LanguageName']==$value['LanguageName']) echo 'selected'?> value="<?php echo isset($value['LanguageId'])? $value['LanguageId']:NULL; ?>"><?php echo isset($value['LanguageName'])? $value['LanguageName']:NULL; ?></option>       
          <?php } ?>    
      </select>
  </div>


  <div class="form-group" >
      <label>Caption Title</label>
      <textarea cols="80" id="editor1" name="caption_title" rows="10"><?php echo isset($details['caption_title'])? $details['caption_title']:NULL; ?></textarea>
   </div> 

   <div class="form-group" >
      <label>Web URL</label>
      <input type="text" name="web_url" value="<?php echo isset($details['co_web_url'])? $details['co_web_url']:NULL; ?>" class="form-control" id="DownloadFileName" placeholder="Name of Person" required>
   </div>

    <div class="form-group">
      <img width="140" height="140" src="<?php echo isset($details['co_logo_path'])? $details['co_logo_path']:NULL; ?>" alt="..." class="img-thumbnail" id="pre_photo">
          
    </div>
    <div class="form-group">
      <label>Company Logo</label>
      <input type="file" onchange="usershow_photo(this)" id="photo" name="co_logo">
    </div>

  

<div class="form-group" >
  <label>Status</label>
   <select class="form-control" required="required" name="status" id="LanguageId">
      <option value="">- - -</option> 
      <option <?php if($details['status']=='1') echo 'selected'  ?> value="1">Active</option>    
      <option <?php if($details['status']=='0') echo 'selected'  ?>   value="0">InActive</option>
  </select>
</div>

</div>

     
</div>

<div class="form-group" >
  <button type="submit" class="btn btn-success" name="update">Update</button> 
</div>

</form>



<script>
  CKEDITOR.replace( 'editor1' );
</script>
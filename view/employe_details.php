<?php
$token = isset($_GET['token'])? $_GET['token'] :NULL;

$data = $obj->details_by_cond('tbl_employee',"id='$token'");
      extract($data);
$path="asset/img/default.jpg";

?>
        
                        
              <div class="col-md-12" style=" background-image:url(asset/img/content_h1.png); margin-top:20px; margin-bottom: 15px; min-height:40px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
              <b>Employee Details Information</b>
              </div>
              <div class="row bio_data">
              <div class="col-md-3"></div>
              <div class="col-md-6"  style="margin: 0 auto;">  
                  
              <div style="border: 1px black solid; text-align: center;">
              <h1 style="margin-top: 5px; color: white;font-size: 32px;"><?php echo isset($data['FullName'])? $data['FullName']:NULL; ?> </h1>
              <img src="
               <?php
               if($data['PhotoPath'] == '')
               {
                echo $path;
               }
               else
               {
                echo $data['PhotoPath'];
               }
               ?>
              " height="180" width="180" style="border: 1px gray solid; margin: 20px; padding:5px"></img>
              
              <p style="font-size:medium; font-family:'ursive'">   
                Employee Nme          : 
              
                 <?php echo $data['employee_name'] ?></br>
                  
                Employee Phone             : <?php echo isset($data['employee_mobile_no'])? $data['employee_mobile_no']:NULL; ?></br>
                Employee Address               : <?php echo isset($data['employee_address'])? $data['employee_address']:NULL; ?></br>
              <hr>
                                      
            <details>
             <summary style="font-size:18px;font-family: serif">Click For More Details..</summary>

              <p style="font-size:medium; font-family:'ursive'">   

            <b>   NID </b>            : <?php echo isset($data['employee_national_id'])? $data['employee_national_id']:NULL; ?></br>
             <b>      User Type  </b>           : <?php echo isset($data['designation'])? $data['designation']:NULL; ?></br>
             <b>    Joining Date   </b>          : <?php echo isset($data['joining_date'])? $data['joining_date']:NULL; ?></br>
            
             
              </p> 
              </details>
              </br>
              </br>
              </div>
            </br>
          </div> 
          <div class="col-md-3"></div>
  </div>
             
       

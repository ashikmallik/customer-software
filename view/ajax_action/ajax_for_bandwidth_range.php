    <?php
    session_start();


    if (!empty($_SESSION['UserId'])) {

        $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
        $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
        $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
        $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
        $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


        //========================================
        include '../../model/oop.php';
        $obj = new Controller();
        include '../../model/Bill.php';
        $bill = new Bill();
        //======= Object Created from Class ======
        //========================================


        date_default_timezone_set('Asia/Dhaka');
        $date_time = date('Y-m-d g:i:sA');
        $date = date('Y-m-d');
        $ip_add = $_SERVER['REMOTE_ADDR'];
        $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
        $notification = "";
        //taking month and years
        $day = date('M-Y');

        $MbRange = array();
        $MbRange2 = array();
        $count = array();
        $finalData = array();
        $minmb = $obj->get_minimum('tbl_agent','int_mb');
        $maxmb = $obj->get_maximum('tbl_agent','int_mb');
        $dif = 0;
        $counter = 0;
        $i=0;
       // $count = $obj->get_count_diff('tbl_agent','ag_id','taka',$minmb,$dif);
        for($minmb ; $minmb <= $maxmb ;)
        {
          $dif = $minmb+1;
          $res = $obj->get_count_diff('tbl_agent','ag_id','int_mb',$minmb,$dif);
          if($res)
         {
     //   for($minmb ; $minmb <= $maxmb ;)
      //  {
            //$dif = $minmb+2;
            $MbRange[] = $minmb;
            $MbRange2[] = $dif;
            $count[] = $obj->get_count_diff('tbl_agent','ag_id','int_mb',$minmb,$dif);
           // $minmb = $dif+.1;
            $counter++;
      //  }
        }
          $minmb = $dif+.1;
         }

        for($i=0;$i<$counter;$i++)
        {
            $finalData[$i] = array("minmax" =>$MbRange[$i].'-'.$MbRange2[$i].' Mbps',
            "total" => $count[$i]);
        }
        echo json_encode($finalData);
        
    }
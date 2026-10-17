<?php

class Controller
{

    //Start DB Connection for Local


    private $host = "localhost";
    private $user = "bsdnewco_user";
    private $db = "bsdnewco_mconnect";
    private $pass = "bsdnewco_user";
 
    


    private $permission ;

    public function __construct()
    {
        $this->con = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->pass);
        $this->permission = $this->getPermissonArray();
    }


// check if this table exists
    function tableExists($table)
    {
        try {

            return $this->con->query("SELECT 1 FROM $table LIMIT 1");

        } catch (Exception $e) {

            return FALSE;
        }
    }


    public function validation($data)
    {
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function login_check($tableName, $adminUser, $adminPass)
    {

        $sql_login = 'SELECT * FROM ' . $tableName . ' WHERE UserName = :user  AND Password = :pass AND Status = 1';
        $login = $this->con->prepare($sql_login);
        $login->execute(array(':user' => $adminUser, ':pass' => $adminPass));
        $total = $login->rowCount();

        if ($total == 1) {

            $data = $login->fetch(PDO::FETCH_ASSOC);
            return isset($data) ? $data : NULL;

        } else {
            return false;
        }

    }
    
    // //nonmasking sms
    // public function sendsms($smsbody, $mobilenum)
    // {
    //             $url = "http://66.45.237.70/api.php";
    //             // $url ="";
    //             $user = $this->getSettingValue('sms', 'user');
    //             $pass = $this->getSettingValue('sms', 'pass');
    //             $sender = $this->getSettingValue('sms', 'sender');
    //             $data= array(
    //             'username'=>$user,
    //             'password'=>$pass,
    //             'number'=>$mobilenum,
    //             'message'=>$smsbody
    //             );
                
    //             $ch = curl_init(); // Initialize cURL
    //             curl_setopt($ch, CURLOPT_URL,$url);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             echo $smsresult = curl_exec($ch);
    //             $p = explode("|",$smsresult);
    // }
    
    
    
    // One to Many |                    
    function sendsms($message, $number) {
            
         $url = "https://bulksmsbd.net/api/smsapi";
     //    $url = "http://new.bulksmsbd.com/api/smsapimany";
        $api_key = "";
        $senderid = "";
        $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $number,
        "message" => $message
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
        }
    
    
    //send Bulk sms many to many 
   function sms_send($smsArray) {
        // $url = "https://bulksmsbd.net/api/smsapimany";
        $url = "http://new.bulksmsbd.com/api/smsapimany";
        $api_key = "";
        $senderid = "";
        $messages = json_encode($smsArray);
        $data = [
            "api_key" => $api_key,
            "senderid" => $senderid,
            "messages" => $messages
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        $json_array=json_decode($response,true);
        $status=$json_array['response_code'];
        $error=$json_array['error_message'];
        echo $httpCode.' S  '.$status.' E'.$error;
        return $response;
    }


// column Data View Condition Wise Function
    public function column_by_cond($column,$table_name, $where_cond)
    {

        $sql = "SELECT $column FROM $table_name WHERE $where_cond";
        $q = $this->con->prepare($sql);
        $q->execute() or die(print_r($q->errorInfo()));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data[$column];
    }

// ==================== Warehouse Wise Total Stock In ====================
public function get_total_stock_in_warehouse($item_id, $warehouse_id) {
    $sql = "SELECT COALESCE(SUM(item_qty), 0) as total 
            FROM tbl_stock 
            WHERE item_id = '$item_id' 
              AND warehouse_id = '$warehouse_id' 
              AND status = 1";

    $data = $this->raw_sql($sql);

    return !empty($data) ? (int)$data[0]['total'] : 0;
}


    public function view_selected_field_by_cond_left_join($table_name1, $table_name2, $table1_col_matched, $table2_col_matched, $table1_col, $table2_col, $where_cond = null)
    {
        $data = array();
        if (!empty($where_cond)) {
            $sql = "SELECT $table1_col, $table_name2.$table2_col FROM $table_name1 LEFT JOIN $table_name2 ON $table_name1.$table1_col_matched = $table_name2.$table2_col_matched WHERE $where_cond";
        } else {
            $sql = "SELECT $table1_col, $table_name2.$table2_col FROM $table_name1 LEFT JOIN $table_name2 ON $table_name1.$table1_col_matched = $table_name2.$table2_col_matched";
        }
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

// ==================== Warehouse Wise Total Stock Out ====================
public function get_total_stock_out_warehouse($item_id, $warehouse_id) {
    $sql = "SELECT COALESCE(SUM(qty), 0) as total 
            FROM tbl_stock_out 
            WHERE item_id = '$item_id' 
              AND warehouse_id = '$warehouse_id'";

    $data = $this->raw_sql($sql);

    return !empty($data) ? (int)$data[0]['total'] : 0;
}
    
    
// Career Registration Function

    public function Reg_user_cond($table_name, $form_data, $where_cond)
    {
        $fields = array_keys($form_data);

        $sql_login = "SELECT * FROM " . $table_name . " WHERE $where_cond";
        $login = $this->con->prepare($sql_login);
        $login->execute(array());
        $total = $login->rowCount();

        if ($total == '0') {

            $sql = "INSERT INTO " . $table_name . "  (`" . implode('`,`', $fields) . "`) VALUES('" . implode("','", $form_data) . "')";
            $q = $this->con->prepare($sql);
            $q->execute() or die(print_r($q->errorInfo()));
            return $this->con->lastInsertId();
        }
    }

//get_sum_bycondion

   public function get_sum_by_cond($columnname, $tablename, $condition = '')
    {

        $sql = "SELECT SUM($columnname) as total  FROM $tablename WHERE $condition";
        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
           if ($row) {
                return $row['total'];
            } else {
                return 0;
            }
    }

    public function get_all_income_with_condition($dateMonth, $dateYear, $condition = '')
    {

        $sql = "SELECT SUM(acc_amount) as amount FROM tbl_account WHERE MONTH(entry_date)='$dateMonth' AND YEAR(entry_date)='$dateYear' $condition";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    // Data Insert Function
    public function Insert_data($table_name, $form_data)
    {
        $fields = array_keys($form_data);

        $sql = "INSERT INTO " . $table_name . "
        (`" . implode('`,`', $fields) . "`)
        VALUES('" . implode("','", $form_data) . "')";

        $q = $this->con->prepare($sql);
        $q->execute() or die(print_r($q->errorInfo()));

        return $this->con->lastInsertId();
    }


// View All Data Function
    public function view_all($table_name)
    {
        $data = array();
        $sql = "SELECT * FROM $table_name";
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    public function view_all_distinct($column_name,$table_name)
    {
        $data = array();
        $sql = "SELECT DISTINCT $column_name FROM $table_name";
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

// View All Data Function
    public function get_all_expense($dateform, $dateto)
    {
        $data = array();
        $sql = "SELECT (SELECT acc_name FROM tbl_accounts_head Where acc_id=acc_head )
        as name,acc_head,SUM(acc_amount)
        as payments FROM tbl_account WHERE acc_type='1' AND MONTH(entry_date)='$dateform' AND YEAR(entry_date)='$dateto'
        GROUP BY acc_head ORDER BY entry_date ASC";

        $q = $this->con->prepare($sql);
        $q->execute();
        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }


    public function get_daywise_expense($date)
    {
        $data = array();
        $sql = "SELECT (SELECT acc_name FROM tbl_accounts_head Where acc_id=acc_head ) as name,acc_head,(acc_amount) as payments, acc_description FROM tbl_account WHERE acc_type='1' AND entry_date='$date' ORDER BY acc_id ASC";

        $q = $this->con->prepare($sql);
        $q->execute();
        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }


    public function get_dayWise_total_expense($date)
    {

        $sql = "SELECT entry_date,SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='1' AND entry_date='$date'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['amount'];
        } else {
            return 0;
        }
    }

    public function get_dayWise_total_other_income($date)
    {

        $sql = "SELECT amount,entry_date FROM vw_others WHERE entry_date='$date'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['amount'];
        } else {
            return 0;
        }
    }

    public function get_dayWise_total_opening_amount($date)
    {

        $sql = "SELECT entry_date,SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='5' AND entry_date='$date'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['amount'];
        } else {
            return 0;
        }
    }


    public function get_dayWise_total_connection_charge($date)
    {

        $sql = "SELECT amount,entry_date FROM vw_connection_charge WHERE entry_date='$date'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['amount'];
        } else {
            return 0;
        }
    }


    public function get_dayWise_total_bill_collection($date)
    {

        $sql = "SELECT amount,entry_date FROM vw_bill_collection WHERE entry_date='$date'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['amount'];
        } else {
            return 0;
        }
    }


//Total Bill Amount
    public function get_total_bill_amount()
    {
        $sql = "SELECT SUM(taka) as taka FROM tbl_agent WHERE (MONTH(entry_date) != MONTH(CURRENT_DATE) OR YEAR(entry_date) != YEAR(CURRENT_DATE)) AND ag_status=1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['taka'];
    }
    //Total Bandwidth
    public function get_total_bandwidth()
    {
        $sql = "SELECT SUM(int_mb) as MB FROM tbl_agent WHERE ag_status=1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['MB'];
    }

//Sum of payment
    public function get_sum_payment($monthDate, $yearDate)
    {
        $sql = "SELECT SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='3' AND MONTH(entry_date)='$monthDate' AND YEAR(entry_date)='$yearDate'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['amount'];
    }

//Sum of Expense
    public function get_sum_expense($monthDate, $yearDate)
    {
        $sql = "SELECT SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='1' AND MONTH(entry_date)='$monthDate' AND YEAR(entry_date)='$yearDate'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    //All Customer Off
    public function get_off_cus($monthDate, $yearDate)
    {
        $sql = "SELECT COUNT(ag_id) as ag_id FROM vw_agent WHERE ag_status='0' AND MONTH(entry_date)='$monthDate' AND YEAR(entry_date)='$yearDate'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    public function get_all_expense_till_previous_month($monthDate, $yearDate)
    {
        $data = array();
        $sql = "SELECT SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='1' AND MONTH(entry_date) < '$monthDate' AND YEAR(entry_date)='$yearDate'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

public function get_all_opening_till_previous_month($monthDate, $yearDate)
    {
        $data = array();
        $sql = "SELECT SUM(acc_amount) as amount FROM tbl_account WHERE acc_type='5' AND MONTH(entry_date) < '$monthDate' AND YEAR(entry_date)='$yearDate'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
// sum of all income
    public function get_all_income_till_previous_month($monthDate, $yearDate)
    {

        $sql = "SELECT SUM(amount) as amount FROM vw_all_income WHERE MONTH(entry_date) < '$monthDate' AND YEAR(entry_date)='$yearDate'";

        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }


// sum of all income
    public function get_all_income($dateMonth, $dateYear)
    {

        $sql = "SELECT SUM(amount) as amount FROM vw_all_income WHERE MONTH(entry_date)='$dateMonth' AND YEAR(entry_date)='$dateYear'";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    // Count All Customer
    public function get_all_cus($dateMonth, $dateYear)
    {

        $sql = "SELECT Count(ag_id) as ag_id FROM vw_agent WHERE MONTH(entry_date)='$dateMonth' AND YEAR(entry_date)='$dateYear' AND ag_status=1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

// View All Data Function
    public function ex_row($dateform, $dateto)
    {
        $data = array();
        $sql = "SELECT (SELECT acc_name FROM tbl_accounts_head Where acc_id=acc_head ) as name, acc_head,SUM(acc_amount) as payments FROM tbl_account WHERE acc_type='1' AND MONTH(entry_date)='$dateform' AND YEAR(entry_date)='$dateto' GROUP BY acc_head ORDER BY entry_date ASC";
        $q = $this->con->prepare($sql);
        $q->execute();
        return $q->rowCount();
    }


// View All Ordered By  Data Function
    public function view_all_ordered_by($table_name, $order)
    {
        $data = array();
        $sql = "SELECT * FROM $table_name ORDER BY $order";
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

// View All Data Condition wise Function
    public function view_all_by_cond($table_name, $where_cond)
    {
        $data = array();
        $sql = "SELECT * FROM $table_name WHERE $where_cond";

        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    // View All Complain Condition wise Function
    public function view_all_complain_by_cond($table_name)
    {
        $data = array();
        $sql = "SELECT comp.*, agnt.ag_name, agnt.ag_mobile_no,agnt.ip, agnt.ag_office_address, emp.employee_name FROM tbl_complains AS comp, tbl_agent AS agnt,tbl_employee AS emp WHERE comp.customer_id = agnt.ag_id AND emp.id = comp.solve_by AND comp.id!=0 AND comp.status != 0 ORDER BY comp.id DESC";

        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

// View All Data for New Customer Condition wise Function
    public function view_new_customer_complain_by_cond($table_name)
    {
        $data = array();
        $sql = "SELECT * FROM $table_name ORDER BY id DESC";
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }



// View All Data Condition wise Function
    public function view_all_by_status($status){
        $data = array();

        $sql = "SELECT comp.*, agnt.ag_name, agnt.ag_mobile_no,agnt.ip,agnt.ag_office_address, emp.employee_name FROM tbl_complains AS comp, tbl_agent AS agnt,tbl_employee AS emp WHERE comp.customer_id = agnt.ag_id AND emp.id = comp.solve_by AND comp.id!=0 AND comp.status = $status ORDER BY comp.id DESC ";
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
// View Condition wise Data for Imminent User
    public function view_new_customer_by_status($status){

       $data = array();
       $sql = "SELECT * FROM tbl_complains_new_user WHERE status = $status ORDER BY id DESC";
       $q = $this->con->prepare($sql);
       $q->execute();

       while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
    return $data;
}

// View All Data Condition wise Function
public function view_all_by_complain(){
    $sql = "SELECT comp.*, agnt.ag_name, emp.employee_name FROM tbl_complains AS comp, tbl_agent AS agnt,tbl_employee AS emp WHERE comp.customer_id = agnt.ag_id AND emp.id = comp.solve_by AND comp.id!=0 ORDER BY comp.id DESC ";
    $q = $this->con->prepare($sql);
    $q->execute();

    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
    return $data;

}


// View Selected Field Data Condition wise Function

public function view_selected_field_by_cond($table_name, $selected_field, $where_cond)
{
    $data = array();
    $sql = "SELECT $selected_field FROM $table_name WHERE $where_cond";
    $q = $this->con->prepare($sql);
    $q->execute();

    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
    return $data;
}


// View Selected Field Data Condition wise Function

public function year_duration_accounts_from_first_entry()
{
    $sql = "SELECT YEAR(CURRENT_DATE) as present_year";
    $q = $this->con->prepare($sql);
    $q->execute();
    $data = $q->fetch(PDO::FETCH_ASSOC);

    return $data['present_year'] - $this->minimum_year_tbl_accounts();
}


// View Selected Field Data Condition wise Function

public function minimum_year_tbl_accounts()
{
    $sql = "SELECT MIN(`entry_date`) as last_date FROM `tbl_account`";
    $q = $this->con->prepare($sql);
    $q->execute();
    $data = $q->fetch(PDO::FETCH_ASSOC);
    return date('Y', strtotime($data['last_date']));
}

// View Selected Field Data Condition wise Function

public function details_selected_field_by_cond($table_name, $selected_field, $where_cond)
{
    $sql = "SELECT $selected_field FROM $table_name WHERE $where_cond";
    $q = $this->con->prepare($sql);
    $q->execute();
    $data = $q->fetch(PDO::FETCH_ASSOC);
    return $data;
}


// Details Data View Condition Wise Function
public function details_by_cond($table_name, $where_cond)
{

    $sql = "SELECT * FROM $table_name WHERE $where_cond";
    $q = $this->con->prepare($sql);
    $q->execute() or die(print_r($q->errorInfo()));
    $data = $q->fetch(PDO::FETCH_ASSOC);
    return $data;
}


// Update Data Function

function Update_data($table_name, $form_data, $where_clause = '')
{

    $whereSQL = '';
    if (!empty($where_clause)) {

        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {

            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }

    $sql = "UPDATE " . $table_name . " SET ";

    $sets = array();
    foreach ($form_data as $column => $value) {
        $sets[] = "`" . $column . "` = '" . $value . "'";
    }
    $sql .= implode(', ', $sets);

    $sql .= $whereSQL;
    $q = $this->con->prepare($sql);

    return $q->execute() or die(print_r($q->errorInfo()));
}


// Delete Data Function
function Delete_data($table_name, $where_cond)
{

    $sql = "DELETE FROM $table_name WHERE $where_cond";
    $q = $this->con->prepare($sql);
    return $q->execute();

}


// Mail Send with Attach file
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message)
{
    $file = $path . $filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
    $header .= "Reply-To: " . $replyto . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message . "\r\n\r\n";
    $header .= "--" . $uid . "\r\n";
        $header .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n"; // use different content types here
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
        $header .= $content . "\r\n\r\n";
        $header .= "--" . $uid . "--";
        if (mail($mailto, $subject, "", $header)) {
            echo "mail send ... OK"; // or use booleans here
        } else {
            echo "mail send ... ERROR!";
        }
    }


// Mail send without attach file

    function mail_send($to, $subject, $message, $from)
    {

        if (mail($to, $subject, $message, "From: $from\n")) {
            //echo "mail send ... OK"; // or use booleans here
        } else {
            echo "mail send ... ERROR!";
        }
    }

// End Mailing Function

    public function Total_Count($table_name, $where_cond)
    {
        $sql = "SELECT * FROM " . $table_name . " WHERE $where_cond";
        $query = $this->con->prepare($sql);
        $query->execute();
        return $query->rowCount();


    }

// View All Data Condition wise Function with left join table
    public function view_all_by_cond_left_join($table_name1, $table_name2, $table1_col_matched, $table2_col_matched, $table2_col, $where_cond = null)
    {
        $data = array();
        if (!empty($where_cond)) {
            $sql = "SELECT $table_name1.*,$table_name2.$table2_col FROM $table_name1 LEFT JOIN $table_name2 ON $table_name1.$table1_col_matched = $table_name2.$table2_col_matched WHERE $where_cond";
        } else {
            $sql = "SELECT $table_name1.*,$table_name2.$table2_col FROM $table_name1 LEFT JOIN $table_name2 ON $table_name1.$table1_col_matched = $table_name2.$table2_col_matched";
        }
        $q = $this->con->prepare($sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

//Start Randome Code

    function Random_Code($chars, $length)
    {

        srand((double)microtime() * 1000000);
        $i = 0;
        $code = '';

        while ($i <= ($length - 1)) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $code = $code . $tmp;
            $i++;


        }
        return $code;
    }


    public function raw_sql($raw_sql)
    {
        $data = array();
        $q = $this->con->prepare($raw_sql);
        $q->execute();

        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }


    public function sessionData($data)
    {

        if (isset($_SESSION["$data"]) && !empty($_SESSION["$data"])) {
            echo $_SESSION["$data"];
            unset($_SESSION["$data"]);
        }
    }


    public function notificationStore($text, $alert = 'danger')
    {
        $_SESSION['count'] = 0; // require for notificationShowRedirect() method

        if (isset($_SESSION["notification"]) && !empty($_SESSION["notification"])) {

            $_SESSION["notification"] .= '<div class="text-center alert alert-' . $alert . ' alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><b>' . $text . '</b></div>';

        } else {

            $_SESSION["notification"] = '<div class="text-center alert alert-' . $alert . ' alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><b>' . $text . '</b></div>';

        }
    }


    public function notificationShow()
    {

        $this->sessionData('notification');
    }


    /*
     *  this method is for while redirect with javascript
     *  the php code already load so before redirect the session is unset.
     *  so using count this code ensure after two times echo the session will unset
     */
    public function notificationShowRedirect()
    {

        if (isset($_SESSION['count'])) {
            $_SESSION['count']++;
        } else {
            $_SESSION['count'] = 0;
        }

        if (isset($_SESSION["notification"]) && !empty($_SESSION["notification"])) {
            echo $_SESSION["notification"];

            if ($_SESSION['count'] > 1) {
                unset($_SESSION["notification"]);
            }
        }
    }


    function searchInDatabase($tableName = '', $keyword = '', $searchField = '', $where_clause = '')
    {
        if (!empty($keyword)) {
            $rawKeyword = trim($keyword);
            $filteredKeyword = preg_replace('#[^A-Za-z 0-9?!.]#i', '', $rawKeyword);
            $splitArrayInSpaceKeyword = preg_split('/[\s]+/', $filteredKeyword);

            $keywordCount = count($splitArrayInSpaceKeyword);

            if ($keywordCount == 1) {

                if (isset($where_clause) && !empty($where_clause)) {
                    $query = "SELECT * FROM `$tableName` WHERE `$where_clause` AND `$searchField` LIKE 
                    '%$splitArrayInSpaceKeyword[0]%'";
                } else {
                    $query = "SELECT * FROM `$tableName` WHERE `$searchField` LIKE '%$splitArrayInSpaceKeyword[0]%'";
                }
            } else {
                if (isset($where_clause) && !empty($where_clause)) {
                    $query = "SELECT * FROM `$tableName` WHERE `$where_clause AND $searchField` LIKE '%$splitArrayInSpaceKeyword[0]%'";
                } else {
                    $query = "SELECT * FROM `$tableName` WHERE `$searchField` LIKE '%$splitArrayInSpaceKeyword[0]%'";
                }

                for ($i = 1; $i < $keywordCount; $i++) {
                    $query .= " AND `$searchField` LIKE '%$splitArrayInSpaceKeyword[$i]%'";
                }
            }

            $rowData = array();
            $sql = $this->con->prepare($query);
            $sql->execute();

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $rowData[] = $row;
            }

            return $rowData;
        }// if keyword is not empty

    }// searchInDatabase finished here

    // get the full permission array from database
    function getPermissonArray(){
        $emptyArray = array();

        if( isset($_SESSION['UserId']) ){
            $permissionArr = $this->details_selected_field_by_cond('_useraccess', 'MenuPermission', 'UserId=' . $_SESSION['UserId']);
            if(isset($permissionArr['MenuPermission']) && !empty($permissionArr['MenuPermission'])){
                return unserialize( $permissionArr['MenuPermission'] );
            }else{
                return $emptyArray;
            }
        }else{
            return $emptyArray;
        }
    }


// check this user's permission by existing in menu permission array
    function hasPermission($userType, $getUrl)
    {
        if (!empty($getUrl)) {

            if($userType == 'SA'){
                return true;
            }else{
                return boolval( in_array($getUrl, $this->permission) );

            }
        }
        return false;
    }

// check this user's permission by existing in menu permission array needed only edit user
    function userHasPermission($getUrl, $userId)
    {
        if (!empty($getUrl) && !empty($userId)) {

            $userAccess = $this->details_selected_field_by_cond('vw_user_info', 'MenuPermission, UserType', 'UserId=' . $userId);
            if($userAccess['UserType'] == 'SA'){
                return true;
            }else{

                if(isset($userAccess['MenuPermission']) && !empty($userAccess['MenuPermission'])){
                    return in_array($getUrl, unserialize($userAccess['MenuPermission']));
                }else{
                    return false;
                }

            }
        }
        return false;
    }

    function printSidebarElement($userType, $getUrl, $menuName){

        if( $this->hasPermission($userType, $getUrl)){
            echo '<li class="list">&nbsp;<a class="texta" href="?q='.$getUrl.'">'.$menuName.'</a></li>';
        }else{
            echo '';
        }
    }

    // permission for edit add ....
    function userWorkPermission($arr, $field){

        if($_SESSION['UserType'] == 'SA'){
            return true;
        }else{
            return in_array($field,$arr);
        }
    }
    // permission for edit add ....
    function userHasWorkPermission($field, $userId)
    {
        if (!empty($field) && !empty($userId)) {

            $userAccess = $this->details_selected_field_by_cond('vw_user_info', 'WorkPermission, UserType', 'UserId=' . $userId);
            if($userAccess['UserType'] == 'SA'){

                return true;
            }else{

                return in_array($field, unserialize($userAccess['WorkPermission']));
            }
        }
        return false;
    }


// get value from table setting for form value show
    function getSettingValue($field, $otherParameter = '')
    {

        if (!empty($otherParameter)) {
            $otherParameter = 'AND other_parameter="' . $otherParameter . '"';
        }

        $settingData = $this->details_by_cond('tbl_setting', 'field="' . $field . '" ' . $otherParameter);

        return $settingData['value'];
    }
    public function get_minimum($table,$column)
    {
        $sql = "SELECT min($column) as qty FROM $table WHERE ag_status=1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['qty'];
    }
    public function get_maximum($table,$column)
    {
        $sql = "SELECT max($column) as qty FROM $table WHERE ag_status=1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['qty'];
    }
     public function get_count_diff($table,$column1,$column2,$value1,$value2)
    {
        $sql = "SELECT count($column1) as qty FROM $table WHERE $column2 BETWEEN $value1 AND $value2 AND ag_status = 1";
        $q = $this->con->prepare($sql);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        return $data['qty'];
    }

    // Stock Start
// ==================== Total Stock In (Warehouse Support) ====================
public function get_total_stock_in($item_id, $warehouse_id = null) {
    $sql = "SELECT COALESCE(SUM(item_qty), 0) as qty 
            FROM tbl_stock 
            WHERE item_id = '$item_id' 
              AND status = '1'";

    if (!empty($warehouse_id)) {
        $sql .= " AND warehouse_id = '$warehouse_id'";
    }

    $data = $this->raw_sql($sql);
    return !empty($data) ? (int)$data[0]['qty'] : 0;
}

// ==================== Total Stock Out (Warehouse Support) ====================
public function get_total_stock_out($item_id, $warehouse_id = null) {
    $sql = "SELECT COALESCE(SUM(item_qty), 0) as qty 
            FROM tbl_stock 
            WHERE item_id = '$item_id' 
              AND status = '0'";

    if (!empty($warehouse_id)) {
        $sql .= " AND warehouse_id = '$warehouse_id'";
    }

    $data = $this->raw_sql($sql);
    return !empty($data) ? (int)$data[0]['qty'] : 0;
}

// ==================== Available Stock (In - Out) ====================
// $exclude_id: ignore this tbl_stock row (used when editing / deleting that row)
public function get_available_stock($item_id, $warehouse_id = null, $exclude_id = null) {
    $sql = "SELECT COALESCE(SUM(CASE WHEN status = '1' THEN item_qty ELSE 0 END), 0)
                 - COALESCE(SUM(CASE WHEN status = '0' THEN item_qty ELSE 0 END), 0) AS qty
            FROM tbl_stock
            WHERE item_id = '" . (int)$item_id . "'";

    if (!empty($warehouse_id)) {
        $sql .= " AND warehouse_id = '" . (int)$warehouse_id . "'";
    }
    if (!empty($exclude_id)) {
        $sql .= " AND id != '" . (int)$exclude_id . "'";
    }

    $data = $this->raw_sql($sql);
    return !empty($data) ? (float)$data[0]['qty'] : 0;
}
    //stock end

}// class Controller


?>
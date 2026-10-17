var SaveStatus=0;

//////////////////  action start  ///////////////
function addautocomplete()
{
    $.post("list.php", function(result){
        //        alert (result)
        if (result){
           //$("#list_rec").append(result);
        }
    });
}
function list(){
//    $("#list_rec").append('list.php');
    //    alert ('allah is one')
//    hide_div();
//    $("#list_rec").show();
//    loader_start();

    location.reload(true);
}
function Save_Rec()
{
    validateResult=true;
    formValidate();
    if(validateResult){
        if (SaveStatus==1){
            $.post("save.php",$("#frm_area").serialize(), function(result){
                if (result){
                    //$("#new_rec").html(result);
//                    list();
                    location.reload(true);
                    loader_close();
                    reset();
                    alertify.set({
                        delay: 3000
                    });
                    alertify.success("Data Save Successfully");
                    return false;
                }
            });
        }else if(SaveStatus==2){
            $.post("update.php",$("#frm_area").serialize(), function(result){
            
                if (result){
                    //$("#edit_rec").html(result);
//                    list();
                    location.reload(true);
                    loader_close();
                    reset();
                    alertify.set({
                        delay: 3000
                    });
                    alertify.success("Data Update Successfully");
                    return false;
                }
            }); 
        }
    }
}

function Load_form(){
    hide_div();
    $("#new_rec").show();
    loader_start();
    $.post("add_frm.php", function(result){
        if (result){
            SaveStatus=1;
            $("#new_rec").html(result);
            $(".mini-title").html('Add From');
            loader_close();
            MenuOffOn('off','on','off','off','on','on','on','on','on','on');
        }
    });
    
}
function edit_form(){
    if($("#rowID").val()==""){
        alertify.set({
            delay: 3000
        });
        alertify.error("Please Select Any Row In The Table");
        return false;
    }else{
        hide_div();
        $("#edit_rec").show();
        loader_start();
        $.post("edit_frm.php",$("#frm_area").serialize(), function(result){
            if (result){
                SaveStatus=2;
                $("#edit_rec").html(result);
                $(".mini-title").html('Edit From');
                loader_close();
                MenuOffOn('off','on','off','off','on','on','on','on','on','on');
            }
        });
    }
}
function details_form(){
    if($("#rowID").val()==""){
        alertify.set({
            delay: 3000
        });
        alertify.error("Please Select Any Row In The Table");
        return false;
    }else{
        hide_div();
        $("#details_rec").show();
        loader_start();
        $.post("details_frm.php",$("#frm_area").serialize(), function(result){
            if (result){
                SaveStatus=2;
                $("#details_rec").html(result);
                $(".mini-title").html('View Detials From');
                loader_close();
                MenuOffOn('off','off','off','off','on','on','on','on','on','on');
            }
        });
    }
}


function Existin_data(elm){
    $("#loader").remove();
    $.post("exist_data.php",{
        name:elm.value
    }, function(result){
        if (result){
            if (result=="Found"){
                $(elm).after('<img id="loader" src="../../system_images/loading-orange.gif" />');
                MenuOffOn('off','off','off','off','off','off','on','on','on','on');
                reset();
                alertify.set({
                    delay: 3000
                });
                alertify.error("Exist user name. try again");
                return false;
            }
            else if (result=="Not Found"){
                $("#loader").remove();
                MenuOffOn('off','on','off','off','on','on','on','on','on','on');
            }
        }
    });
}

function referrals_type_fnc(){
    if($("#referral_type").val()=="Agent"){
        $(".div_agent").slideDown();
        $(".div_referrals").slideUp();
        $(".div_pax_contact").slideUp();
    }else if($("#referral_type").val()=="Referrals"){
        $(".div_agent").slideUp();
        $(".div_referrals").slideDown();
        $(".div_pax_contact").slideDown();
    }else if($("#referral_type").val()=="Office"){
        $(".div_agent").slideUp();
        $(".div_referrals").slideUp();
        $(".div_pax_contact").slideDown();
    }else{
        $(".div_agent").slideUp();
        $(".div_referrals").slideUp();
        $(".div_pax_contact").slideUp();
    }
}

//function service_std_toure_fnc(){
//    if($("#service_id").val()=="DI-000001" || $("#service_id").val()=="DI-000007"){
//        $(".div_country").slideDown();
//        $(".div_flight_date").slideUp();
//        $(".div_sector").slideUp();
//    }else if($("#service_id").val()=="DI-000006" || $("#service_id").val()=="DI-000007"){
//        $(".div_country").slideDown();
//        $(".div_flight_date").slideUp();
//        $(".div_sector").slideUp();
//    }else if($("#service_id").val()=="DI-000002" || $("#service_id").val()=="DI-000004" || $("#service_id").val()=="DI-000005"){
//        $(".div_country").slideUp();
//        $(".div_flight_date").slideDown();
//        $(".div_sector").slideDown();
//    }else{
//        $(".div_country").slideUp();
//        $(".div_flight_date").slideUp();
//        $(".div_sector").slideUp();
//    }
//}

function service_std_toure_fnc(){
    if($("#service_id").val()=="1"){
        $(".div_air_line_id").slideDown();
        $(".div_new_flight_date").slideDown();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideDown();
        $(".div_air_line_pnr").slideDown();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="2"){
        $(".div_air_line_id").slideDown();
        $(".div_new_flight_date").slideDown();
        $(".div_old_flight_date").slideDown();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideDown();
        $(".div_air_line_pnr").slideDown();
        $(".div_gds_pnr").slideDown();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="3"){
        $(".div_air_line_id").slideDown();
        $(".div_new_flight_date").slideDown();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideDown();
        $(".div_air_line_pnr").slideDown();
        $(".div_gds_pnr").slideDown();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="4" || $("#service_id").val()=="5"){
        $(".div_air_line_id").slideDown();
        $(".div_new_flight_date").slideDown();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideDown();
        $(".div_air_line_pnr").slideUp();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="6" || $("#service_id").val()=="11"){
        $(".div_air_line_id").slideUp();
        $(".div_new_flight_date").slideUp();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideDown();
        $(".div_sector").slideUp();
        $(".div_air_line_pnr").slideUp();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="7"){
        $(".div_air_line_id").slideUp();
        $(".div_new_flight_date").slideUp();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideDown();
        $(".div_sector").slideUp();
        $(".div_air_line_pnr").slideUp();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideDown();
        $(".div_course_name").slideDown();
        $(".div_email_address").slideUp();
    }else if($("#service_id").val()=="8"){
        $(".div_air_line_id").slideUp();
        $(".div_new_flight_date").slideUp();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideUp();
        $(".div_air_line_pnr").slideUp();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideDown();
    }else{
        $(".div_air_line_id").slideUp();
        $(".div_new_flight_date").slideUp();
        $(".div_old_flight_date").slideUp();
        $(".div_change_flight_date").slideUp();
        $(".div_country_id").slideUp();
        $(".div_sector").slideUp();
        $(".div_air_line_pnr").slideUp();
        $(".div_gds_pnr").slideUp();
        $(".div_collage_name").slideUp();
        $(".div_course_name").slideUp();
        $(".div_email_address").slideUp();
    }
}

function sms_send_confirm(){
    if($("#working_status").val()=="Done"){
        $(".div_sms_send").slideDown();        
    }else{
        $(".div_sms_send").slideUp();
    }
}

function sms_number_fnc(){
    $("#sms_no").val('');
    if($("#referral_type").val()=="Agent"){
        $.post("ajaxdata.php", {
            agent_id: $("#agent_id").val()
        }, function (result){
            if(result){
                $("#sms_no").val(result);
            }
        })
    }else if($("#referral_type").val()=="Referrals"){
        $("#sms_no").val($("#contact_no").val());
    }else if($("#referral_type").val()=="Office"){
        $("#sms_no").val($("#contact_no").val());
    }else{
        alert ("Please select referrals type! try again.");
    }
}


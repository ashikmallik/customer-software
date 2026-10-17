var validateResult=true; 
////////////////////////////////////////////////////
function loader_start(){
    $("#loader_div").show();
    $("#loader_div").append("<div class='widget-body'><img src='../../system_images/loading-black.gif' ></div>");
}
function loader_close(){
    $("#loader_div").html('');
    $("#loader_div").hide();
}
function refresh_frm(){
    location.reload(true);
}
function back_list(){
    reset();
    alertify.confirm("Are You Sure .... You Back to Page?", function (e) {
        if (e) {
            list();
        } else {
        //            alertify.error("You've clicked Cancel");
        }
    });
    return false;
}
function hide_div(){
    $("#gridlist").hide();
    $("#list_rec").hide();
    $("#new_rec").hide();
    $("#edit_rec").hide();
    $("#details_rec").hide();
    $("#list_rec").html('');
    $("#new_rec").html('');
    $("#edit_rec").html('');
    $("#details_rec").html('');
    $("#loader_div").html('');
}
function get_rowID(elm, count){
    $("#rowID").val(elm);
    $(".pointer").children('td, th').css("background", "");
    $("#tr_id"+count).children('td, th').css("background", "rgb(229,230,150)");
    
//    $(".pointer").children('td, th').click(function(){$("#right_popup_menu").fadeOut();})
//    $(document).ready(function(){ 
//        document.oncontextmenu = function() {
//            return false;
//        };
//        $(document).mousedown(function(e){ 
//            if( e.button == 2 ) { 
//                var toppx=e.pageY+'px';
//                var leftpx=e.pageX+'px';
//                
//                $("#right_popup_menu").css('top', toppx);
//                $("#right_popup_menu").css('left', leftpx);
//                
//                $("#right_popup_menu").fadeIn();
//                return false; 
//            } 
//            return true; 
//        }); 
//    });
    
}


//function MenuOffOn(nw,sv,edt,dls,dlt,rpt,rfs,bck,src,pnt){
//    if (nw=='off'){$("#add_btn").removeAttr('onclick');}else {$("#add_btn").Attr('onclick:new_rec()');}
//    if (sv=='off'){$("#save_btn").removeAttr('onclick','');} else {$("#save_btn").attr('onclick','Save_Rec()');}
//    if (edt=='off'){$("#edit_btn").attr('onclick','');} else {$("#edit_btn").attr('onclick','Edit_Rec()');}
//    if (dls=='off'){$("#details_btn").attr('onclick','');} else {$("#details_btn").attr('onclick','Details_Rec()');}
//    if (dlt=='off'){$("#delete_btn").attr('onclick','');} else {$("#delete_btn").attr('onclick','Delete_Rec()');}
//    if (rpt=='off'){$("#report_btn").attr('onclick','');} else {$("#report_btn").attr('onclick','Report_Rec()');}
//    if (rfs=='off'){$("#refresh_btn").attr('onclick','');} else {$("#refresh_btn").attr('onclick','refresh_frm()');}
//    if (bck=='off'){$("#back_btn").attr('onclick','');} else {$("#back_btn").attr('onclick','back_list()');}
//    if (src=='off'){$("#search_btn").attr('onclick','');} else {$("#search_btn").attr('onclick','Search_Rec()');}
//    if (pnt=='off'){$("#printer_btn").attr('onclick','');} else {$("#printer_btn").attr('onclick','Print_Rec()');}
//}

function MenuOffOn(nw,sv,edt,dls,dlt,rpt,rfs,bck,src,pnt){
    if (nw=='off'){
        $("#add_btn").hide();
    }else {
        $("#add_btn").show();
    }
    if (sv=='off'){
        $("#save_btn").hide();
    } else {
        $("#save_btn").show();
    }
    if (edt=='off'){
        $("#edit_btn").hide();
    } else {
        $("#edit_btn").show();
    }
    if (dls=='off'){
        $("#view_btn").hide();
    } else {
        $("#view_btn").show();
    }
    if (dlt=='off'){
        $("#delete_btn").hide();
    } else {
        $("#delete_btn").show();
    }
    if (rpt=='off'){
        $("#report_btn").hide();
    } else {
        $("#report_btn").show();
    }
    if (rfs=='off'){
        $("#refresh_btn").hide();
    } else {
        $("#refresh_btn").show();
    }
    if (bck=='off'){
        $("#back_btn").hide();
    } else {
        $("#back_btn").show();
    }
    if (src=='off'){
        $("#search_btn").hide();
    } else {
        $("#search_btn").show();
    }
    if (pnt=='off'){
        $("#printer_btn").hide();
    } else {
        $("#printer_btn").show();
    }
}

function logout(){
    window.location.href="../../module/dashboard/log_out.php";
}
///////////////////////////// Start  ME Alert Box Edit by maraj ///////////

function ME_Alert(title, body){
    if(title==""){
        var title_txt="Alert !";
    }else{
        var title_txt="Alert !";
    }
    $('body').append('<div id="alert_box" class="alert_contant" ><div class="alert_title">'+title_txt+'</div>'+body+'<div class="alert_footer"><div class="alert_ok_btn" onclick="alert_box_close()">OK</div></div></div>');
    $('body').append('<div class="MEAlert_shadow"></div>');
}
function ME_CAlert(title, body){
    $('body').append('<div id="Calert_box" class="alert_contant" ><div class="alert_title">'+title+'</div>'+body+'<div class="alert_footer"><div class="alert_ok_btn" onclick="Calert_box_close()">No</div><div class="alert_ok_btn" onclick="Calert_yes_btn()">Yes</div></div></div>');
    $('body').append('<div class="MEAlert_shadow"></div>');
}

function shadow_body(){
    $('body').append('<div class="MEAlert_shadow"></div>');
}
  
function shadow_body_close(){
    $(".MEAlert_shadow").remove();
}
function alert_box_close(){
    $("#alert_box").remove();
    $(".MEAlert_shadow").remove();
}
function Calert_box_close(){
    $("#Calert_box").remove();
    $(".MEAlert_shadow").remove();
}
function Calert_yes_btn(){
    list();
    $("#Calert_box").remove();
    $(".MEAlert_shadow").remove();
}
///////////////////////////// End  ME Alert Box Edit by maraj ///////////
//////////////////////////// Form Validation ///////////////////////////////////////////////
function formValidate()
{  
    $("#save_btn").hide();
    $('form').find('[validate]').each(function(index){
        if($(this).attr("validate").indexOf("Require")!=-1)
        //        if($(this).attr("validate"))
        {
            if($(this).val().length == 0)
            {
                //            $(this).after('Please Fill Up Field');
                $("#save_btn").show();
                var thisattr=$(this).attr('placeholder');
                $(this).css("background", "#FF4A4A");
                $(this).bind('focus', function() {
                    $(this).css("background", "");
                });
                validateResult=false;
                reset();
                alertify.set({
                    delay: 1000
                });
                alertify.error("Please Fill Up Marked Field ("+thisattr+")");
                return false;
            }
        } 
        if($(this).attr("validate").indexOf("Email")!=-1)
        {
            var mystring= new String($(this).val());
            var myregExp=/^\w+((-\w+)|(\.\w+))*\@\w+((\.|-)\w+)*\.\w+$/;
            var answerIdx=mystring.search(myregExp)
            if(($(this).val().length != 0)&&(answerIdx==-1))
            {
                $("#save_btn").show();
                $(this).css("background", "#FF4A4A");
                $(this).bind('focus', function() {
                    $(this).css("background", "");
                });
                validateResult=false;
                reset();
                alertify.set({
                    delay: 1000
                });
                alertify.error("Fill Up Email Address Correctly ..!");
                return false;
            }
        }
        if($(this).attr("validate").indexOf("Mobile")!=-1)
        {
            if(($(this).val().length != 0)&&($(this).val().length <11))
            {
                $("#save_btn").show();
                $(this).css("background", "#FF4A4A");
                $(this).bind('focus', function() {
                    $(this).css("background", "");
                });
                validateResult=false;
                reset();
                alertify.set({
                    delay: 1000
                });
                alertify.error("Fill Up Mobile Number Correctly ..!");
                return false;
            }
        } 
        if($(this).attr("validate").indexOf("Picture")!=-1)
        {
            if($(this).val().length != 0)
            {           
                $("#save_btn").show();     
                if (!($(this).val().match(/(?:gif|jpg|png)$/)) )
                {
                    $(this).css("background", "#FF4A4A");
                    $(this).bind('focus', function() {
                        $(this).css("background", "");
                    });
                    validateResult=false;
                    reset();
                    alertify.set({
                        delay: 1000
                    });
                    alertify.error("Please Only Upload GIF, JPG, PNG File");
                    return false;
                }
            }
        }
        if($(this).attr("validate").indexOf("Picture_jpg")!=-1)
        {
            if($(this).val().length != 0)
            {                
                if (!($(this).val().match(/(?:jpg)$/)) )
                {
                    $("#save_btn").show();
                    $(this).css("background", "#FF4A4A");
                    $(this).bind('focus', function() {
                        $(this).css("background", "");
                    });
                    validateResult=false;
                    reset();
                    alertify.set({
                        delay: 1000
                    });
                    alertify.error("Please Only Upload JPG File");
                    return false;
                }
            }
        }
        if($(this).attr("validate").indexOf("ZIP")!=-1)
        {
            if($(this).val().length != 0)
            {                
                if (!($(this).val().match(/(?:zip|ZIP)$/)) )
                {
                    $("#save_btn").show();
                    $(this).css("background", "#FF4A4A");
                    $(this).bind('focus', function() {
                        $(this).css("background", "");
                    });
                    validateResult=false;
                    reset();
                    alertify.set({
                        delay: 1000
                    });
                    alertify.error("Please Only Upload ZIP File");
                    return false;
                }
            }
        }    
    });    
}
function regexCheck(value, re) {
    return String(value).search (re) != -1;
}  
//////////////////////////// Form Validation ///////////////////////////////////////////////

////////////////////////////  start tree onload function edit by maraj //////////////////////\


function ME_Tree(){
    //
    // first example
    $("#browser").treeview();
	
    // second example
    $("#navigation").treeview({
        persist: "location",
        collapsed: true,
        unique: true
    });
	
    // third example
    $("#red").treeview({
        animated: "fast",
        collapsed: true,
        unique: true,
        persist: "cookie",
        toggle: function() {
            window.console && console.log("%o was toggled", this);
        }
    });
	
    // fourth example
    $("#black, #gray").treeview({
        control: "#treecontrol",
        persist: "cookie",
        cookieId: "treeview-black"
    });

}

function numbersOnly(e) // Numeric Validation 
{
    var unicode=e.charCode? e.charCode : e.keyCode
    if (unicode!=8)
    {
        if ((unicode<2534||unicode>2543)&&(unicode<48||unicode>57))
        {
            return false;                       
        }
    }
} 
////////////////////////////  start tree onload function edit by maraj //////////////////////
//function print_rpt(){
//    URL="../../libraries/lib/Print_a4_Eng.php?selLayer=PrintArea";
//    day = new Date();
//    id = day.getTime();
//    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=yes,scrollbars=yes ,location=0,statusbar=0 ,menubar=yes,resizable=1,width=880,height=600,left = 20,top = 50');");        
//}

////////////////////// Start Menu brackcumb ///////////////////
function bread_comb(sm_id, st_id){
    $("#nav_sub").hide();
    $.post("../../module/common/load_bread_cumb.php", {
        sm_id:sm_id, 
        st_id:st_id
    }, function(result){
        if(result){
            $("#nav_sub").html(result);
        }
    });
}

////////////////////// End Menu brackcumb ///////////////////
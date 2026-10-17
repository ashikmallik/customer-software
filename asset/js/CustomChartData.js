/**
 * Created by mehedi on 2/16/17 Only for control Chart in dashboard.
 */

$(document).ready(function () {
    $.ajax({

        url: "view/ajax_action/ajax_for_dashBoard.php",
        method: "GET",
        dataType: "json",

        success: function (data) {

            var acc_amount = [];
            var chart_date = [];
            var exp_amount = [];
            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

            /*
             ========= Chart for Last 12 months bill collection =============
             */
            for(i = 0; i < 12; i++){
                if(data[i].inc_date || data[i].exp_amount){
                    var dateForCheck = new Date(data[i].inc_date);
                    if(dateForCheck.getTime() != 0){
                        dateForShowChart= new Date(data[i].inc_date);
                    }else{
                        dateForShowChart= new Date(data[i].exp_date);
                    }
                    var monthInNumber = dateForShowChart.getMonth();
                    var monthInString = monthNames[monthInNumber];
                    var Year = (dateForShowChart.getFullYear() - 2000);

                    chart_date.push(monthInString+" "+Year);
                    // if(data[i].inc_date){ acc_amount.push(data[i].acc_amount);}else{ acc_amount.push("0");}
                    // if(data[i].exp_amount){ exp_amount.push(data[i].exp_amount);}else{ acc_amount.push("0");}
                    acc_amount.push(data[i].acc_amount);
                    exp_amount.push(data[i].exp_amount);
                }
            }

            var charData = {
                labels: chart_date,
                datasets: [{

                    label: 'Collection',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255,99,132,1)',
                    hoverBackgroundColor: 'rgba(255, 99, 132, 0.7)',
                    hoverBorderColor: 'rgba(255, 99, 132, 1)',
                    data: acc_amount,
                    borderWidth: 1,
                },
                    {

                        label: 'Expense',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        hoverBackgroundColor: 'rgba(54, 162, 235, 0.7)',
                        hoverBorderColor: 'rgba(54, 162, 235, 1)',
                        data: exp_amount,
                        borderWidth: 1,
                    }],
            };

            var ctx = $('#accountsChart');

            var accountsChart = new Chart(ctx, {
                type: 'bar',
                data: charData,
            });

            /*
             ========= Chart for Income and Expense Chart =============
             */

            /*var pieChartData = {
                labels: [
                    "Income",
                    "Expense",
                ],
                datasets: [
                    {
                        data: [data[13]['incomeThisMonth'], data[13]['expenseThisMonth']],
                        backgroundColor: [
                            "#03A678",
                            "#E87E04",
                        ],
                        hoverBackgroundColor: [
                            "#03996e",
                            "#d37508",
                        ]
                    }]
            };
            var pieChartx = $('#expenseIncome');

            var pieChart = new Chart(pieChartx, {
                type: 'doughnut',
                data: pieChartData,
            });*/

        }// succes function data
    }); // Ajax

/*New and OffCutomer last 12 month*/

$.ajax({

        url: "view/ajax_action/ajax_for_new_off.php",
        method: "GET",
        dataType: "json",

        success: function (data) {

            var acc_ag_id = [];
            var chart_date = [];
            var off_ag_id = [];
            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

            /*
             ========= Chart for Last 12 months bill collection =============
             */
            for(i = 0; i < 12; i++){
                if(data[i].inc_date || data[i].off_ag_id){
                    var dateForCheck = new Date(data[i].inc_date);
                    if(dateForCheck.getTime() != 0){
                        dateForShowChart= new Date(data[i].inc_date);
                    }else{
                        dateForShowChart= new Date(data[i].exp_date);
                    }
                    var monthInNumber = dateForShowChart.getMonth();
                    var monthInString = monthNames[monthInNumber];
                    var Year = (dateForShowChart.getFullYear() - 2000);

                    chart_date.push(monthInString+" "+Year);
                    // if(data[i].inc_date){ acc_amount.push(data[i].acc_amount);}else{ acc_amount.push("0");}
                    // if(data[i].exp_amount){ exp_amount.push(data[i].exp_amount);}else{ acc_amount.push("0");}
                    acc_ag_id.push(data[i].acc_ag_id);
                    off_ag_id.push(data[i].off_ag_id);
                }
            }

            var charData = {
                labels: chart_date,
                datasets: [{

                    label: 'New Customer',
                    backgroundColor: 'rgba(46, 179, 102, 0.8)',
                    borderColor: 'rgba(46,179,102,.9)',
                    hoverBackgroundColor: 'rgba(46, 179, 102, 0.8)',
                    hoverBorderColor: 'rgba(46, 179, 102, 1)',
                    data: acc_ag_id,
                    borderWidth: 1,
                },
                    {

                        label: 'Inactive Customer',
                        backgroundColor: 'rgba(229, 96, 70, 0.5)',
                        borderColor: 'rgba(229, 96, 70, 1)',
                        hoverBackgroundColor: 'rgba(229, 96, 70, 0.7)',
                        hoverBorderColor: 'rgba(229, 96, 70, 1)',
                        data: off_ag_id,
                        borderWidth: 1,
                    }],
            };

            var ctx = $('#cusChart');

            var accountsChart = new Chart(ctx, {
                type: 'bar',
                data: charData,
            });

        }// succes function data
    }); // Ajax



                      /*End New And off Customer last 12 month*/

/*Price Range*/

// $.ajax({

//         url: "view/ajax_action/ajax_for_price_range.php",
//         method: "GET",
//         dataType: "json",

//         success: function (data) {
//             var minmax = [];
//             var total = [];
//             for(i = 0; i < data.length; i++){ 
//               minmax.push(data[i].minmax);
//               total.push(data[i].total);
//             }

//             var charData = {
//                 labels: minmax,
//                 datasets: [{

//                     label: 'Total Customer',
//                     backgroundColor: 'rgba(20, 86, 156,.8)',
//                     borderColor: 'rgba(20, 86, 156,.9)',
//                     hoverBackgroundColor: 'rgba(20, 86, 156,1)',
//                     hoverBorderColor: 'rgba(20, 86, 156,.9)',
//                     data: total,
//                     borderWidth: 1,
//                 }],
//             };

//             var ctx = $('#RangeChart');

//             var accountsChart = new Chart(ctx, {
//                 type: 'horizontalBar',
//                 data: charData,
//             });
//     }
// });
/*End Price Range*/

/*Bandwidth Range*/
// $.ajax({

//         url: "view/ajax_action/ajax_for_bandwidth_range.php",
//         method: "GET",
//         dataType: "json",

//         success: function (data) {
//             var minmax = [];
//             var total = [];
//             for(i = 0; i < data.length; i++){ 
//               minmax.push(data[i].minmax);
//               total.push(data[i].total);
//             }

//             var charData = {
//                 labels: minmax,
//                 datasets: [{

//                     label: 'Total Customer',
//                     backgroundColor: 'rgba(40, 85, 187)',
//                     borderColor: 'rgba(40, 85, 187)',
//                     hoverBackgroundColor: 'rgba(40, 85, 187)',
//                     hoverBorderColor: 'rgba(40, 85, 187)',
//                     data: total,
//                     borderWidth: 1,
//                 }],
//             };

//             var ctx = $('#BandWidthChart');

//             var accountsChart = new Chart(ctx, {
//                 type: 'horizontalBar',
//                 data: charData,
//             });
//     }
// });
/*End Bandwidth Range*/
    /*
     ========= Chart for Active and Inactive Chart =============
     */
    /*$.ajax({

        url: "view/ajax_action/ajax_data_return.php",
        method: "GET",
        dataType: "json",

        success: function (result) {
            $('.preData').hide();

            var pieChartData = {
                labels: [
                    "Total Bill",
                    "Total Due",
                ],
                datasets: [
                    {
                        data: [result.totalBill, result.duePayment],
                        backgroundColor: [
                            "#9B59B6",
                            "#4183D7",
                        ],
                        hoverBackgroundColor: [
                            "#9550b2",
                            "#306bb5",
                        ]
                    }]
            };
            var pieChartx = $('#billInfoChart');

            var pieChart = new Chart(pieChartx, {
                type: 'pie',
                data: pieChartData,
            });
        }// succes function data
    }); // Ajax*/



}); // document ready function
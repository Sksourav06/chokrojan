var seatTypeColors  = ["bg-light-black","bg-light-gray","bg-light-gray","bg-gray","bg-dark-gray","bg-gray","bg-dark-gray"];
var tripListUpdateInterval  = 60000; //after every 60 second
var tripSeatplanUpdateInterval  = 30000; //after every 30 second
var primary = '#24C58A';
var success = '#1BC5BD';
var info = '#8950FC';
var warning = '#FFA800';
var danger = '#F64E60';
var userRole = {
    superAdmin      : 1,
    admin           : 2,
    counterMaster   : 3,
    counterManager  : 4,
    accountant      : 5,
    operationManager: 9,
    operationAdmin  : 10,
    operationMaster : 11,
    manager         : 12
};

const sweetAlertModal = Swal.mixin({
    customClass: {
        confirmButton   : 'btn btn-success px-8',
        cancelButton    : 'btn btn-gray px-8'
    },
    buttonsStyling      : false
})

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function routeWiseCommission(reqUrl) {
    $("#route_com_div").html('<i class="fa fa-spinner fa-2x fa-spin text-danger"></i>');
    $.ajax({
        type    : 'GET',
        url     : reqUrl,
        success : function (response) {
            $("#route_com_div").html(response.data);
        }
    });
}

function loadRouteWiseCommissionData(reqUrl)
{
    $('#is_commission_counter').on('change', function () {
        let isComm = $(this).val();
        if (isComm == 1) {
            routeWiseCommission(reqUrl);
        } else {
            $("#route_com_div").html("");
        }
    });
    if($('#is_commission_counter').val() == 1){
        routeWiseCommission(reqUrl);
    }
}

function routeStations(reqUrl) {
    $("#route_stations_div").html('<i class="fa fa-spinner fa-2x fa-spin text-danger"></i>');
    $.ajax({
        type    : 'GET',
        url     : reqUrl,
        success : function (response) {
            $("#route_stations_div").html(response.data);
        }
    });
}

function getUserSelectedRoleWiseCounterSetPermission(roleId) {
    if( roleId==3 || roleId==4 || roleId==9 || roleId==11 || roleId==12 ){
        return true;
    }else {
        return false;
    }
}

function showSeatLayout(reqUrl,seat_layout_id, hiddenSeats) {
    if(hiddenSeats==null) hiddenSeats="";
    $("#seatplan-div").html('<i class="fa fa-spinner fa-2x fa-spin text-danger"></i>');
    var seatLayout = Array();
    $.ajax({
        type    : 'GET',
        url     : reqUrl,
        success : function (response) {
            var seatLayout  = response.data.seatLayout;
            var seatPlansLd = response.data.seatPlansLd;
            var seatPlansUd = response.data.seatPlansUd;
            var noOfSeat    = seatLayout.number_of_seats;
            var noOfRow     = seatLayout.number_of_rows;
            var noOfCol     = seatLayout.number_of_columns;
            var noOfRowDD   = seatLayout.dd_number_of_rows;
            var noOfColDD   = seatLayout.dd_number_of_columns;
            var deckTypeId  = seatLayout.deck_type_id;
            var seatPlanStr = '';
            if(noOfRow>0 && noOfCol>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Lower Deck</div>';
                for(var ri=0; ri<noOfRow; ri++){
                    for(var ci=0; ci<noOfCol; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansLd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansLd[rowColm]!=undefined){
                            var seatInfo        = seatPlansLd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = seatInfo.seat_type.name;
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" onclick="SeatSelectDeselect(this);" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            if(noOfRowDD>0 && noOfColDD>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Upper Deck</div>';
                for(var ri=0; ri<noOfRowDD; ri++){
                    for(var ci=0; ci<noOfColDD; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansUd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansUd[rowColm]!=undefined){
                            var seatInfo        = seatPlansUd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = seatInfo.seat_type.name;
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" onclick="SeatSelectDeselect(this);" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            $("#seatplan-div").html(seatPlanStr);
            $('body').tooltip({
                selector: '.seatInfo'
            });
        }
    });
}

function showPermittedSeatLayout(reqUrl, scheduleId, hiddenSeats, postData, type, divId) {
    if(hiddenSeats==null) hiddenSeats="";
    $("#"+divId).html('<i class="fa fa-spinner fa-2x fa-spin text-danger"></i>');
    var seatLayout = Array();

    $.ajax({
        type    : 'POST',
        url     : reqUrl,
        data    : postData,
        success : function (response) {
            var seatLayout  = response.data.seatLayout;
            var seatPlansLd = response.data.seatPlansLd;
            var seatPlansUd = response.data.seatPlansUd;
            var permSeats   = response.data.permSeats;
            var noOfSeat    = seatLayout.number_of_seats;
            var noOfRow     = seatLayout.number_of_rows;
            var noOfCol     = seatLayout.number_of_columns;
            var noOfRowDD   = seatLayout.dd_number_of_rows;
            var noOfColDD   = seatLayout.dd_number_of_columns;
            var deckTypeId  = seatLayout.deck_type_id;
            var seatPlanStr = '';
            if(noOfRow>0 && noOfCol>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Lower Deck</div>';
                for(var ri=0; ri<noOfRow; ri++){
                    for(var ci=0; ci<noOfCol; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansLd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansLd[rowColm]!=undefined){
                            var seatInfo        = seatPlansLd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = ''; //seatInfo.seat_type.name;
                            if(permSeats[seatInfo.seat_number]!=undefined){
                                seatBoxColor    = 'bg-light-success';
                                seatTitle       = permSeats[seatInfo.seat_number];
                            }
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            if(noOfRowDD>0 && noOfColDD>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Upper Deck</div>';
                for(var ri=0; ri<noOfRowDD; ri++){
                    for(var ci=0; ci<noOfColDD; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansUd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansUd[rowColm]!=undefined){
                            var seatInfo        = seatPlansUd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = ''; //seatInfo.seat_type.name;
                            if(permSeats[seatInfo.seat_number]!=undefined){
                                seatBoxColor    = 'bg-light-success';
                                seatTitle       = permSeats[seatInfo.seat_number];
                            }
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            $("#"+divId).html(seatPlanStr);
            $('body').tooltip({
                selector: '.seatInfo'
            });
        }
    });
}

function showAndSetPermittedSeatLayout(reqUrl, scheduleId, hiddenSeats, permittedSeats, postData, type, divId) {
    if(hiddenSeats==null) hiddenSeats="";
    if(permittedSeats==null) permittedSeats="";
    $("#"+divId).html('<i class="fa fa-spinner fa-2x fa-spin text-danger"></i>');
    var seatLayout = Array();

    $.ajax({
        type    : 'POST',
        url     : reqUrl,
        data    : postData,
        success : function (response) {
            var seatLayout  = response.data.seatLayout;
            var seatPlansLd = response.data.seatPlansLd;
            var seatPlansUd = response.data.seatPlansUd;
            //var permSeats   = response.data.permSeats;
            var noOfSeat    = seatLayout.number_of_seats;
            var noOfRow     = seatLayout.number_of_rows;
            var noOfCol     = seatLayout.number_of_columns;
            var noOfRowDD   = seatLayout.dd_number_of_rows;
            var noOfColDD   = seatLayout.dd_number_of_columns;
            var deckTypeId  = seatLayout.deck_type_id;
            var seatPlanStr = '';
            if(noOfRow>0 && noOfCol>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Lower Deck</div>';
                for(var ri=0; ri<noOfRow; ri++){
                    for(var ci=0; ci<noOfCol; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansLd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansLd[rowColm]!=undefined){
                            var seatInfo        = seatPlansLd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = ''; //seatInfo.seat_type.name;
                            /*if(permSeats[seatInfo.seat_number]!=undefined){
                                seatBoxColor    = 'bg-light-success';
                                seatTitle       = permSeats[seatInfo.seat_number];
                            }*/
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(permittedSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = 'bg-success';
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" onclick="SeatSelectDeselectForPermission(this);" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            if(noOfRowDD>0 && noOfColDD>0 && seatLayout!=""){
                if(deckTypeId==2) seatPlanStr += '<div class="bsp bsp-1 text-center bg-light-info">Upper Deck</div>';
                for(var ri=0; ri<noOfRowDD; ri++){
                    for(var ci=0; ci<noOfColDD; ci++){
                        var rowColm = ri+","+ci;
                        if(seatPlansUd[rowColm]==undefined){
                            seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"></div>';
                        }
                        else if(seatPlansUd[rowColm]!=undefined){
                            var seatInfo        = seatPlansUd[rowColm];
                            var seatBoxColor    = seatTypeColors[seatInfo.seat_type_id];
                            var seatTitle       = ''; //seatInfo.seat_type.name;
                            /*if(permSeats[seatInfo.seat_number]!=undefined){
                                seatBoxColor    = 'bg-light-success';
                                seatTitle       = permSeats[seatInfo.seat_number];
                            }*/
                            if(hiddenSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = seatTypeColors[0];
                                seatTitle       = "Blocked Seat";
                            }
                            if(permittedSeats.includes(seatInfo.seat_number)){
                                seatBoxColor    = 'bg-success';
                            }
                            if(seatInfo.seat_number=="#"){
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" title="Driver Seat" class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0" title="Driver"><i class="fas fa-radiation-alt pr-0"></i></button></div>';
                            }else {
                                seatPlanStr += '<div class="bsp bsp-'+noOfCol+'"><button type="button" data-toggle="tooltip" data-placement="top" class="seatInfo btn btn-sm btn-block px-1 py-1 my-0 '+seatBoxColor+'" title="'+seatTitle+'" onclick="SeatSelectDeselectForPermission(this);" data-stid="'+seatInfo.seat_type_id+ '" value="'+seatInfo.row_column+'">' + seatInfo.seat_number + '</button></div>';
                            }
                        }
                    }
                }
            }
            $("#"+divId).html(seatPlanStr);
            $('body').tooltip({
                selector: '.seatInfo'
            });
        }
    });
}
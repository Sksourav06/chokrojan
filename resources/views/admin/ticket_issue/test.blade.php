<div id="tripdetails-684601" class="tripdetails row border border-top-0 m-0 py-3 bg-success-o-40" style=""
    bis_skin_checked="1">
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12 col-12 pb-2" bis_skin_checked="1">
                <label for="station_from_to">Change (From Station ⟹ To Station)</label>
                <select name="station_from_to" id="station_from_to" class="form-control form-control-sm"
                    onchange="stationToStationSeatPlanShow(this.value);">
                    <option value="1,70,700" selected="">
                        Dhaka ⟹ Sylhet (700)
                    </option>
                    <option value="1,73,600">
                        Dhaka ⟹ Sherpur (600)
                    </option>
                    <option value="1,72,530">
                        Dhaka ⟹ Shayestaganj (530)
                    </option>
                </select>
            </div>
            <div class="col-sm-8 col-8 pl-4 pb-1" bis_skin_checked="1">
                <h3 class="pt-1 m-0 text-danger">01_SP</h3>
            </div>
            <div class="col-sm-4 col-4 pb-1 pl-0 text-center" bis_skin_checked="1">
                <div class="col-12 p-0" bis_skin_checked="1"><button id="btn-refresh"
                        class="btn btn-sm btn-pill btn-block bg-info text-white" onclick="updateShowingTripSeatplan();"
                        style="margin:0px 0px; padding:6px 2px;" disabled="disabled"><i
                            class="fas fa-sync-alt fa-1x text-white pr-0 fa-spin"></i> Refresh</button></div>
            </div>
            <!-- <div class="col-sm-3 col-3 pr-0">
            <div class="row">
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-blocked mb-1 px-1 py-2">Blocked</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-engaged mb-1 px-1 py-2">Engaged</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-selected mb-1 px-1 py-2">Selected</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-booked-male mb-1 px-1 py-2">Booked(M)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-booked-female mb-1 px-1 py-2">Booked(F)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-male mb-1 px-1 py-2">Sold(M)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-female mb-1 px-1 py-2">Sold(F)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-online mb-1 px-1 py-2">Online</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-vip mb-1 px-1 py-2">VIP</button></div>
                <div class="col-sm-12"><button id="btn-refresh" class="btn btn-sm btn-pill btn-block bg-info text-white" onclick="updateShowingTripSeatplan();" style="margin:30px 0px; padding:6px 2px;"><i class="fas fa-sync-alt fa-1x text-white pr-0"></i> Refresh</button></div>
            </div>
        </div>-->
            <div class="col-sm-12 col-12" id="seatplan-layout-div" bis_skin_checked="1">
                <div id="seatplan-div" class="rounded bg-white p-1"
                    style="border:1px dotted #CCCCCC; width: 100%; min-height:175px; float: left;" bis_skin_checked="1">
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title=""
                            class="seatInfo btn btn-sm btn-secondary btn-block p-1 my-0"
                            data-original-title="Driver Seat"><i class="fas fa-radiation-alt pr-0"></i></button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A1"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384781" data-stid="2" value="700"
                            data-original-title="xxxx, 00000000000 (Sold) by Md. Nurul Haque from Fokirapool Counter-2">A1</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B1"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">B1</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C1"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6383311" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by Md. Mijan 2 from (Panthapath)Kalabagan Counter">C1</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D1"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6383311" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by Md. Mijan 2 from (Panthapath)Kalabagan Counter">D1</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A2"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384757" data-stid="2" value="700"
                            data-original-title="xxxx, 00000000000 (Sold) by Sharif from Gabtoli Counter">A2</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B2"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384777" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 01640633800 (Sold) by Imran from Saydabad-1 Hujur barir gate">B2</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C2"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384767" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by BRTC counter from Kallyanpur BRTC Counter">C2</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D2"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384770" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by BRTC counter from Kallyanpur BRTC Counter">D2</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A3"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384767" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by BRTC counter from Kallyanpur BRTC Counter">A3</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B3"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384767" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by BRTC counter from Kallyanpur BRTC Counter">B3</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C3"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384795" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by Nurunnobi from Janapath Counter-1">C3</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D3"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384795" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 11111111111 (Sold) by Nurunnobi from Janapath Counter-1">D3</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A4"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384774" data-stid="2" value="700"
                            data-original-title="xxxx, 00000000000 (Sold) by Abu Bakar from Fokirapool Counter-2">A4</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B4"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384774" data-stid="2" value="700"
                            data-original-title="xxxx, 00000000000 (Sold) by Abu Bakar from Fokirapool Counter-2">B4</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C4"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384774" data-stid="2" value="700"
                            data-original-title="xxxx, 00000000000 (Sold) by Abu Bakar from Fokirapool Counter-2">C4</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D4"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D4</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A5"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384785" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 01640633800 (Sold) by Imran from Saydabad-1 Hujur barir gate">A5</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B5"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384785" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 01640633800 (Sold) by Imran from Saydabad-1 Hujur barir gate">B5</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C5"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384785" data-stid="2" value="700"
                            data-original-title="eeeeeeeeee, 01640633800 (Sold) by Imran from Saydabad-1 Hujur barir gate">C5</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D5"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D5</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A6"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384810" data-stid="2" value="700"
                            data-original-title="md. mossarop, 01843922723 (Sold) by Md. Masum from Sign Board Counter">A6</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B6"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384810" data-stid="2" value="700"
                            data-original-title="md. mossarop, 01843922723 (Sold) by Md. Masum from Sign Board Counter">B6</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C6"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-sold-male"
                            onclick="seatSelect(this);" data-tid="6384810" data-stid="2" value="700"
                            data-original-title="md. mossarop, 01843922723 (Sold) by Md. Masum from Sign Board Counter">C6</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D6"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D6</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A7"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">A7</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B7"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">B7</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C7"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">C7</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D7"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D7</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A8"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">A8</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B8"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">B8</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C8"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">C8</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D8"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D8</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="A9"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">A9</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="B9"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">B9</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="C9"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">C9</button>
                    </div>
                    <div class="bsp bsp-5" bis_skin_checked="1">
                        <button type="button" data-toggle="tooltip" data-placement="top" title="" id="D9"
                            class="seatInfo btn btn-sm  btn-block px-1 py-1 my-0 bg-light-gray"
                            onclick="seatSelect(this);" data-tid="" data-stid="2" value="700"
                            data-original-title="Economy Class">D9</button>
                    </div>
                </div>
                <script type="text/javascript">
                    trip_boarding_counters = [{ "id": 11507788, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1090, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Gabtoli Counter" }, { "id": 11507807, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 2384, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kachpur-Sly Branch" }, { "id": 11507808, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 2383, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kachpur-CTG Branch" }, { "id": 11507789, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1105, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:15:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kallyanpur BRTC Counter" }, { "id": 11507790, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1091, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:25:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "(Panthapath)Kalabagan Counter" }, { "id": 11507791, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1092, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool-1 (Hotel Asar)" }, { "id": 11507792, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1093, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool Counter-2" }, { "id": 11507793, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1088, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool-3 Mosjid Market" }, { "id": 11507794, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1094, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kamlapur Counter" }, { "id": 11507795, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1096, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Titi Para Counter" }, { "id": 11507800, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1106, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad-1 Hujur barir gate" }, { "id": 11507799, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1100, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad RK College Counter" }, { "id": 11507798, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1098, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad Counter-2" }, { "id": 11507796, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1097, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Janapath Counter-1" }, { "id": 11507797, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1089, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Janapath Counter-2" }, { "id": 11507801, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1099, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Shonirakhra Counter" }, { "id": 11507802, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1095, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Sign Board Counter" }, { "id": 11507803, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1107, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Chittagong Road Counter (Block)" }];
                    trip_dropping_counters = [{ "id": 11507806, "company_id": 7, "trip_id": 684601, "station_id": 70, "counter_id": 789, "required_time": "06:00:00", "arrival_time": "2025-11-21 10:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Humayun Counter - 1" }];
                    $('[data-toggle="tooltip"]').tooltip({
                        container: '#seatplan-div'
                    });
                </script>
            </div>
        </div>
    </div>
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-7 col-7 pr-1" bis_skin_checked="1">
                        <label for="boarding_counter_id"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i> Boarding
                            Counter</label>
                        <select name="boarding_counter_id" id="boarding_counter_id"
                            class="form-control form-control-sm">
                            <option value="">Select Boarding Counter...</option>
                            <option value="1090">
                                04:00 AM -- Gabtoli Counter
                            </option>
                            <option value="2384">
                                04:00 AM -- Kachpur-Sly Branch
                            </option>
                            <option value="2383">
                                04:00 AM -- Kachpur-CTG Branch
                            </option>
                            <option value="1105">
                                04:15 AM -- Kallyanpur BRTC Counter
                            </option>
                            <option value="1091">
                                04:25 AM -- (Panthapath)Kalabagan Counter
                            </option>
                            <option value="1092">
                                04:30 AM -- Fokirapool-1 (Hotel Asar)
                            </option>
                            <option value="1093">
                                04:30 AM -- Fokirapool Counter-2
                            </option>
                            <option value="1088">
                                04:30 AM -- Fokirapool-3 Mosjid Market
                            </option>
                            <option value="1094">
                                04:30 AM -- Kamlapur Counter
                            </option>
                            <option value="1096">
                                04:45 AM -- Titi Para Counter
                            </option>
                            <option value="1106">
                                05:30 AM -- Saydabad-1 Hujur barir gate
                            </option>
                            <option value="1100">
                                05:30 AM -- Saydabad RK College Counter
                            </option>
                            <option value="1098">
                                05:30 AM -- Saydabad Counter-2
                            </option>
                            <option value="1097">
                                05:30 AM -- Janapath Counter-1
                            </option>
                            <option value="1089">
                                05:30 AM -- Janapath Counter-2
                            </option>
                            <option value="1099">
                                05:45 AM -- Shonirakhra Counter
                            </option>
                            <option value="1095">
                                05:45 AM -- Sign Board Counter
                            </option>
                            <option value="1107">
                                05:45 AM -- Chittagong Road Counter (Block)
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-5 col-5 pl-1" bis_skin_checked="1">
                        <label for="boarding_place">Boarding Place</label>
                        <input type="text" class="form-control form-control-sm" id="boarding_place"
                            name="boarding_place" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-7 col-7 pr-1" bis_skin_checked="1">
                        <label for="dropping_counter_id"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i> Dropping
                            Counter</label>
                        <select name="dropping_counter_id" id="dropping_counter_id"
                            class="form-control form-control-sm">
                            <option value="">Select Dropping Counter...</option>
                            <option value="789" selected="">
                                10:00 AM -- Humayun Counter - 1
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-5 col-5 pl-1" bis_skin_checked="1">
                        <label for="dropping_place">Dropping Place</label>
                        <input type="text" class="form-control form-control-sm" id="dropping_place"
                            name="dropping_place" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-12" bis_skin_checked="1">
                        <table class="table table-sm table-bordered bg-white mb-0">
                            <tbody id="sell-seat-table">
                                <tr>
                                    <th width="10%" style="text-align:center">#</th>
                                    <th width="20%" style="text-align:left">SEAT</th>
                                    <th width="40%" style="text-align:left">TYPE</th>
                                    <th width="30%" style="text-align:right">FARE</th>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align:right" colspan="3">Sub Total :</td>
                                    <td style="text-align:right" id="sub-total">0</td>
                                </tr>
                                <tr>
                                    <td style="text-align:right" colspan="3">Goods Charge / Extra Fare : ⊕</td>
                                    <td style="text-align:right">
                                        <input type="text" class="form-control form-control-sm" id="goods-charge"
                                            value="" onkeyup="addGoodsCharge(this.value)" style="height: 25px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right" colspan="3">Discount : ⊝</td>
                                    <td style="text-align:right">
                                        <input type="text" class="form-control form-control-sm" id="discount-amount"
                                            value="" onkeyup="subDiscountAmount(this.value)" style="height: 25px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:right" colspan="3">Callerman Commission : ⊝</td>
                                    <td style="text-align:right">
                                        <input type="text" class="form-control form-control-sm"
                                            id="callerman-commission" value=""
                                            onkeyup="setCallermanCommissionAmount(this.value)" style="height: 25px;">
                                    </td>
                                </tr>
                                <tr id="callerman_mobile_tr" style="display: none;">
                                    <td style="text-align:right" colspan="3">Callerman Mobile No. : </td>
                                    <td style="text-align:right">
                                        <input type="text" class="form-control form-control-sm" id="callerman-mobile"
                                            value="" onkeyup="setCallermanMobileNumber(this.value)"
                                            style="height: 25px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:left" colspan="2">Total Seat : <span id="total-seat">0</span>
                                    </td>
                                    <td style="text-align:right">Grand Total :</td>
                                    <td style="text-align:right" id="grand-total">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row px-3 pb-3" bis_skin_checked="1">
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-booked-male mb-1 px-1 py-1">Booked(M)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-booked-female mb-1 px-1 py-1">Booked(F)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-male mb-1 px-1 py-1">Sold(M)</button></div>
                    <div class="col-3 p-0 px-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-female mb-1 px-1 py-1">Sold(F)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-blocked mb-1 px-1 py-1">Blocked</button></div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-engaged mb-1 px-1 py-1">Engaged</button></div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-selected mb-1 px-1 py-1">Selected</button></div>
                    <div class="col-2 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-online mb-1 px-1 py-1">Online</button>
                    </div>
                    <div class="col-1 p-0 px-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-vip mb-1 px-1 py-1">VIP</button></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-12" bis_skin_checked="1">
                        <label for="passenger_mobile"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i> Passenger
                            Mobile Number <span id="number-check"></span></label>
                        <input type="text" class="form-control form-control-sm" id="passenger_mobile"
                            name="passenger_mobile" maxlength="11" value=""
                            onkeyup="getPassengerInfoByMobile(this.value)">
                    </div>
                    <div class="form-group col-sm-12" bis_skin_checked="1">
                        <label for="passenger_name"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i> Passenger
                            Full Name</label>
                        <input type="text" class="form-control form-control-sm" id="passenger_name"
                            name="passenger_name" value="">
                    </div>
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <select name="passenger_gender" id="passenger_gender" class="form-control form-control-sm">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <select name="passenger_nationality" id="passenger_nationality"
                            class="form-control form-control-sm">
                            <option value="AF">Afghanistan</option>
                            <option value="AL">Albania</option>
                            <option value="DZ">Algeria</option>
                            <option value="DS">American Samoa</option>
                            <option value="AD">Andorra</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AQ">Antarctica</option>
                            <option value="AG">Antigua and Barbuda</option>
                            <option value="AR">Argentina</option>
                            <option value="AM">Armenia</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaijan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrain</option>
                            <option value="BD" selected="">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BY">Belarus</option>
                            <option value="BE">Belgium</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermuda</option>
                            <option value="BT">Bhutan</option>
                            <option value="BO">Bolivia</option>
                            <option value="BA">Bosnia and Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BV">Bouvet Island</option>
                            <option value="BR">Brazil</option>
                            <option value="IO">British Indian Ocean Territory</option>
                            <option value="BN">Brunei Darussalam</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodia</option>
                            <option value="CM">Cameroon</option>
                            <option value="CA">Canada</option>
                            <option value="CV">Cape Verde</option>
                            <option value="KY">Cayman Islands</option>
                            <option value="CF">Central African Republic</option>
                            <option value="TD">Chad</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CX">Christmas Island</option>
                            <option value="CC">Cocos (Keeling) Islands</option>
                            <option value="CO">Colombia</option>
                            <option value="KM">Comoros</option>
                            <option value="CK">Cook Islands</option>
                            <option value="CR">Costa Rica</option>
                            <option value="HR">Croatia (Hrvatska)</option>
                            <option value="CU">Cuba</option>
                            <option value="CY">Cyprus</option>
                            <option value="CZ">Czech Republic</option>
                            <option value="CD">Democratic Republic of the Congo</option>
                            <option value="DK">Denmark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominica</option>
                            <option value="DO">Dominican Republic</option>
                            <option value="TP">East Timor</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egypt</option>
                            <option value="SV">El Salvador</option>
                            <option value="GQ">Equatorial Guinea</option>
                            <option value="ER">Eritrea</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Ethiopia</option>
                            <option value="FK">Falkland Islands (Malvinas)</option>
                            <option value="FO">Faroe Islands</option>
                            <option value="FJ">Fiji</option>
                            <option value="FI">Finland</option>
                            <option value="FR">France</option>
                            <option value="FX">France, Metropolitan</option>
                            <option value="GF">French Guiana</option>
                            <option value="PF">French Polynesia</option>
                            <option value="TF">French Southern Territories</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GK">Guernsey</option>
                            <option value="GN">Guinea</option>
                            <option value="GW">Guinea-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HM">Heard and Mc Donald Islands</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IR">Iran (Islamic Republic of)</option>
                            <option value="IQ">Iraq</option>
                            <option value="IE">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="CI">Ivory Coast</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JE">Jersey</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="KP">Korea, Democratic People's Republic of</option>
                            <option value="KR">Korea, Republic of</option>
                            <option value="XK">Kosovo</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Lao People's Democratic Republic</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libyan Arab Jamahiriya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macau</option>
                            <option value="MG">Madagascar</option>
                            <option value="MW">Malawi</option>
                            <option value="MY">Malaysia</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="TY">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="FM">Micronesia, Federated States of</option>
                            <option value="MD">Moldova, Republic of</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="ME">Montenegro</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibia</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="NL">Netherlands</option>
                            <option value="AN">Netherlands Antilles</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NU">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="MK">North Macedonia</option>
                            <option value="MP">Northern Mariana Islands</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau</option>
                            <option value="PS">Palestine</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PN">Pitcairn</option>
                            <option value="PL">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="CG">Republic of Congo</option>
                            <option value="RE">Reunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russian Federation</option>
                            <option value="RW">Rwanda</option>
                            <option value="KN">Saint Kitts and Nevis</option>
                            <option value="LC">Saint Lucia</option>
                            <option value="VC">Saint Vincent and the Grenadines</option>
                            <option value="WS">Samoa</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome and Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="RS">Serbia</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="SO">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="GS">South Georgia South Sandwich Islands</option>
                            <option value="SS">South Sudan</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SH">St. Helena</option>
                            <option value="PM">St. Pierre and Miquelon</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SJ">Svalbard and Jan Mayen Islands</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syrian Arab Republic</option>
                            <option value="TW">Taiwan</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania, United Republic of</option>
                            <option value="TH">Thailand</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad and Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TM">Turkmenistan</option>
                            <option value="TC">Turks and Caicos Islands</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="US">United States</option>
                            <option value="UM">United States minor outlying islands</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VA">Vatican City State</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VG">Virgin Islands (British)</option>
                            <option value="VI">Virgin Islands (U.S.)</option>
                            <option value="WF">Wallis and Futuna Islands</option>
                            <option value="EH">Western Sahara</option>
                            <option value="YE">Yemen</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <input type="text" class="form-control form-control-sm" id="passenger_email"
                            name="passenger_email" placeholder="Email Address" value="">
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <input type="text" class="form-control form-control-sm" id="passenger_passport"
                            name="passenger_passport" placeholder="Passport Number" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <div class="input-icon" bis_skin_checked="1">
                            <input type="text" class="form-control form-control-sm" id="keep_book_until"
                                name="keep_book_until" placeholder="Keep booking until" value="">
                            <span><i class="fas fa-calendar-alt text-muted"></i></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <div class="checkbox-inline rounded bg-white"
                            style="border: 1px solid #E5EAEE; padding: 0.45rem 0.75rem;" bis_skin_checked="1">
                            <label class="checkbox checkbox-outline checkbox-success">
                                <input type="checkbox" name="vip_seat" id="vip_seat">
                                <span></span>
                                VIP Passenger
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <select name="def_counter_id" id="def_counter_id" class="form-control form-control-sm"
                            onchange="showCounterMasterList(this.value);">
                            <option value="">Select Counter...</option>
                            <option value="1091">Dhaka -- (Panthapath)Kalabagan Counter</option>
                            <option value="2314">Dhaka -- Badda Link Road</option>
                            <option value="2368">Dhaka -- Chittagong Road Counter</option>
                            <option value="738">Dhaka -- Dhaka</option>
                            <option value="1669">Dhaka -- Dholaiapar</option>
                            <option value="3191">Dhaka -- ECB Cottor</option>
                            <option value="3264">Dhaka -- ECB Cottor Army market</option>
                            <option value="1093">Dhaka -- Fokirapool Counter-2</option>
                            <option value="1092">Dhaka -- Fokirapool-1 (Hotel Asar)</option>
                            <option value="1088">Dhaka -- Fokirapool-3 Mosjid Market</option>
                            <option value="1090">Dhaka -- Gabtoli Counter</option>
                            <option value="1899">Dhaka -- Golapbag counter</option>
                            <option value="1097">Dhaka -- Janapath Counter-1</option>
                            <option value="1089">Dhaka -- Janapath Counter-2</option>
                            <option value="2383">Dhaka -- Kachpur-CTG Branch</option>
                            <option value="2384">Dhaka -- Kachpur-Sly Branch</option>
                            <option value="1105">Dhaka -- Kallyanpur BRTC Counter</option>
                            <option value="3192">Dhaka -- Kalshi</option>
                            <option value="1094">Dhaka -- Kamlapur Counter</option>
                            <option value="1108">Dhaka -- Kochukhet Counter</option>
                            <option value="1102">Dhaka -- Middle Badda Counter</option>
                            <option value="3261">Dhaka -- Mirpur-1</option>
                            <option value="1109">Dhaka -- Mirpur-10 (1) Benaroshi Polli</option>
                            <option value="1101">Dhaka -- Mirpur-10 (2) gol chottor</option>
                            <option value="1110">Dhaka -- Norda Counter</option>
                            <option value="1104">Dhaka -- Savar Counter</option>
                            <option value="1098">Dhaka -- Saydabad Counter-2</option>
                            <option value="1100">Dhaka -- Saydabad RK College Counter</option>
                            <option value="1106">Dhaka -- Saydabad-1 Hujur barir gate</option>
                            <option value="1099">Dhaka -- Shonirakhra Counter</option>
                            <option value="1095">Dhaka -- Sign Board Counter</option>
                            <option value="2180">Dhaka -- Teguria</option>
                            <option value="1096">Dhaka -- Titi Para Counter</option>
                            <option value="741">Shayestaganj -- Shayestaganj</option>
                            <option value="836">Sherpur -- Sherpur</option>
                            <option value="788">Sylhet -- AL - Amin</option>
                            <option value="789">Sylhet -- Humayun Counter - 1</option>
                            <option value="790">Sylhet -- Humayun Counter - 2</option>
                            <option value="792">Sylhet -- Mager Gate</option>
                            <option value="796">Sylhet -- Mager Gate -2</option>
                            <option value="791">Sylhet -- Sobhani Ghat</option>
                            <option value="742">Sylhet -- Sylhet</option>
                            <option value="1742">Sylhet -- Tarminal Counter</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <span id="search-counter-master" style="position: absolute; left: 15px; top: 8px;"></span>
                        <select name="def_counter_master_id" id="def_counter_master_id"
                            class="form-control form-control-sm">
                        </select>
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <i class="far fa-clock text-danger fa-2x"> <span id="ticket-timer"></span></i>
                    </div>
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <input type="hidden" name="count_comm" id="count_comm" value="0">
                        <button id="confirm1" name="confirm" class="btn btn-sm btn-pill btn-danger"
                            style="min-width: 40%;" value="Seat Sell" onclick="confirmTicketIssue(1);">Seat
                            Sell</button>
                    </div>
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <button type="button" class="btn btn-sm btn-pill btn-info" style="min-width: 40%;"
                            onclick="showTripSheet(684601);">Trip Sheet</button>
                        <input type="button" name="reset" class="btn btn-sm btn-pill btn-gray-light"
                            style="min-width: 40%;" value="Reset" onclick="resetSeatIssueForm();">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        trip_boarding_counters = [{ "id": 11507788, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1090, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Gabtoli Counter" }, { "id": 11507807, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 2384, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kachpur-Sly Branch" }, { "id": 11507808, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 2383, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kachpur-CTG Branch" }, { "id": 11507789, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1105, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:15:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kallyanpur BRTC Counter" }, { "id": 11507790, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1091, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:25:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "(Panthapath)Kalabagan Counter" }, { "id": 11507791, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1092, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool-1 (Hotel Asar)" }, { "id": 11507792, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1093, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool Counter-2" }, { "id": 11507793, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1088, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Fokirapool-3 Mosjid Market" }, { "id": 11507794, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1094, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Kamlapur Counter" }, { "id": 11507795, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1096, "required_time": "00:00:00", "arrival_time": "2025-11-21 04:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Titi Para Counter" }, { "id": 11507800, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1106, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad-1 Hujur barir gate" }, { "id": 11507799, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1100, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad RK College Counter" }, { "id": 11507798, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1098, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Saydabad Counter-2" }, { "id": 11507796, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1097, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Janapath Counter-1" }, { "id": 11507797, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1089, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:30:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Janapath Counter-2" }, { "id": 11507801, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1099, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Shonirakhra Counter" }, { "id": 11507802, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1095, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Sign Board Counter" }, { "id": 11507803, "company_id": 7, "trip_id": 684601, "station_id": 1, "counter_id": 1107, "required_time": "00:00:00", "arrival_time": "2025-11-21 05:45:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Chittagong Road Counter (Block)" }];
        trip_dropping_counters = [{ "id": 11507806, "company_id": 7, "trip_id": 684601, "station_id": 70, "counter_id": 789, "required_time": "06:00:00", "arrival_time": "2025-11-21 10:00:00", "created_by": null, "updated_by": null, "created_at": "2025-10-22T21:00:15.000000Z", "updated_at": null, "deleted_at": null, "counter_name": "Humayun Counter - 1" }];
        trip_status = 'ready';
        $("#callerman_mobile_tr").hide();
        if (trip_status == 'cancelled') {
            $("#tripdetails-" + trip_id + " #confirm0").hide();
            $("#tripdetails-" + trip_id + " #confirm1").hide();
        }
        $('[data-toggle="tooltip"]').tooltip({
            container: '#seatplan-div'
        });
    </script>
</div> ai rokom hobe tumi just value gula change kore deo
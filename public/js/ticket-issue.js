(function($, window, document) {
    "use strict";

    // ===================================================================
    // 1. GLOBAL VARIABLE INITIALIZATION
    // ===================================================================
    const TRIP_ID = $("#schedule_id").val() || null;
    const DEFAULT_SEAT_FARE = typeof window.SEAT_FARE !== 'undefined' ? window.SEAT_FARE : 0;

    let selectedSeats = [];
    let currentFare = DEFAULT_SEAT_FARE;
    let lockExpirationTime = null;
    let countdownInterval = null;


    // ===================================================================
    // 2. CALCULATION & TABLE UPDATE FUNCTIONS
    // ===================================================================

    function parseFare() {
        const val = $("#station_from_to").val();
        if (!val) return currentFare || 0;

        const p = val.split(",");
        const fare = parseFloat(p[2]) || 0;

        currentFare = fare;
        return fare;
    }

    /**
     * 🔥 NEW: টেবিল আপডেট করার ফাংশন
     * এটি 'sell-seat-table' বডিতে ডাটা পুশ করবে।
     */
    function updateSeatTable() {
    const tableBody = $('#sell-seat-table');
    
    // 🚨 প্রথম সারি (হেডার) বাদে নিচের সব সারি মুছে ফেলুন
    tableBody.find("tr:gt(0)").remove(); 

    if (selectedSeats.length === 0) {
        // কোনো সিট না থাকলে মেসেজ দেখাবে (Colspan ছাড়া চাইলে এটি বাদ দিতে পারেন)
        tableBody.append('<tr class="no-seat"><td colspan="4" class="text-center text-muted">No seat selected</td></tr>');
        return;
    }

    const fare = parseFare();
    const busType = (window.DB_BUS_TYPE && window.DB_BUS_TYPE !== "") 
                    ? window.DB_BUS_TYPE 
                    : "Standard";

    selectedSeats.forEach((seat, index) => {
        const row = `
            <tr>
                <td style="text-align:center">${index + 1}</td>
                <td style="text-align:left">${seat}</td>
                <td style="text-align:left">${busType}</td>
                <td style="text-align:right">${fare.toFixed(2)}</td>
            </tr>
        `;
        tableBody.append(row);
    });
}

    function calcGrand() {
        const fare = parseFare();
        const subtotal = selectedSeats.length * fare;
        $("#sub-total").text(subtotal.toFixed(2));

        let total = subtotal;

        const readFloat = (selector) => {
            const value = $(selector).val();
            if (value === null || value === undefined) return 0;
            return parseFloat(String(value).replace(/,/g, '')) || 0;
        };

        const goods = readFloat("#goods-charge");
        const discount = readFloat("#discount-amount");
        const commission = readFloat("#callerman-commission");

        total += goods;
        total -= (discount + commission);
        if (total < 0) total = 0;

        $("#grand-total").text(total.toFixed(2));
        $("#total-seat").text(selectedSeats.length);
    }

    window.addGoodsCharge = calcGrand;
    window.subDiscountAmount = calcGrand;
    window.setCallermanCommissionAmount = function() {
        calcGrand();
        const commissionVal = parseFloat($("#callerman-commission").val()) || 0;
        if (commissionVal > 0) {
            $("#callerman_mobile_tr").show();
        } else {
            $("#callerman_mobile_tr").hide();
        }
    };
    window.setCallermanMobileNumber = function() {};
    window.showCounterMasterList = function(value) { console.log("Counter Master List value:", value); };


    // ===================================================================
    // 3. SEAT RENDERING AND FETCHING
    // ===================================================================

    function renderSeats(seatArray) {
        const $grid = $("#seatplan-div");
        if (!$grid.length) return;

        $grid.empty();
        const seatMap = {};
        seatArray.forEach(s => { seatMap[s.seat_number] = s; });

        let maxRow = 0, maxCol = 0;
        seatArray.forEach(s => {
            const m = /^([A-Z]+)(\d+)$/i.exec(s.seat_number);
            if (m) {
                const rowStr = m[1].toUpperCase();
                const rowIndex = rowStr.charCodeAt(0) - 64;
                const colIndex = parseInt(m[2], 10);
                if (rowIndex > maxRow) maxRow = rowIndex;
                if (colIndex > maxCol) maxCol = colIndex;
            }
        });

        if (maxRow === 0) maxRow = 4;
        if (maxCol === 0) maxCol = 4;

        for (let r = 1; r <= maxRow; r++) {
            const $row = $('<div class="seat-row d-flex justify-content-center mb-1"></div>');
            for (let c = 1; c <= maxCol; c++) {
                const seatName = String.fromCharCode(64 + r) + c;
                const seatObj = seatMap[seatName] || { seat_number: seatName, status: 'available' };

                let btnClass = 'btn-outline-primary';
                let onClickAction = `seatSelect(this)`;
                let tooltip = seatObj.tooltip || `${seatName} Available`;

                if (seatObj.status === 'sold' || seatObj.status === 'booked') {
                    const gender = seatObj.gender === 'female' ? 'female' : 'male';
                    btnClass = `bg-${seatObj.status}-${gender}`;
                    onClickAction = `openSoldTicketModal(${seatObj.ticket_id})`;
                    tooltip = `${seatObj.customer_name || 'Passenger N/A'} <br>${(seatObj.status).toUpperCase()} from ${seatObj.counter_name || 'Counter N/A'}`;
                } else if (seatObj.status === 'engaged') {
                    btnClass = 'bg-engaged';
                    onClickAction = '';
                    tooltip = `Locked by ${seatObj.counter_name || 'Unknown Counter'}<br>Expires at ${seatObj.expiry_time || 'N/A'}`;
                }

                const $btn = $(`<button type="button" class="seatBtn seatInfo btn btn-sm btn-block px-1 py-1 my-0 ${btnClass}" data-seat="${seatName}" data-toggle="tooltip" data-html="true" title="${tooltip}">${seatName}</button>`);
                if (onClickAction) $btn.attr('onclick', onClickAction);

                const $col = $('<div class="bsp bsp-5 p-1" style="width: 70px;"></div>');
                $col.append($btn);
                $row.append($col);

                if (maxCol > 2 && c == Math.floor(maxCol / 2)) {
                    $row.append('<div class="bus-aisle-gap mx-6"></div>');
                }
            }
            $grid.append($row);
        }

        $('[data-toggle="tooltip"]').tooltip();
        selectedSeats.forEach(seat => {
            $(`[data-seat="${seat}"]`).removeClass("btn-outline-primary bg-engaged").addClass("btn-success bg-selected");
        });

        calcGrand();
        updateSeatTable(); // সিট রেন্ডার হওয়ার সময় টেবিল আপডেট
    }

    window.reloadSeats = function() {
        const val = $("#station_from_to").val();
        if (!val) {
            alert("Please select a route.");
            return;
        }
        const parts = val.split(',');
        const fromId = parts[0], toId = parts[1];
        const scheduleId = $("#schedule_id").val();

        $("#btn-refresh").prop('disabled', true).find('i').addClass('fa-spin');
        $("#seatplan-div").html("<div class='text-center p-5'>Loading seats...</div>");

        fetchSeats(fromId, toId, scheduleId).then(renderSeats).catch(err => {
            $("#seatplan-div").html(`<div class='alert alert-danger text-center'>${err.message || "Unable to load seats."}</div>`);
        }).finally(() => {
            $("#btn-refresh").prop('disabled', false).find('i').removeClass('fa-spin');
        });
    }

    function fetchSeats(fromStationId, toStationId, scheduleId) {
    return new Promise((resolve, reject) => {
        if (!fromStationId || !toStationId) {
            return reject(new Error("Invalid route selected"));
        }

        const url = `/admin/ticket-issue/seats/${fromStationId}-${toStationId}?trip_id=${scheduleId || TRIP_ID || ''}`;
        console.log("Seat API URL:", url); // 👈 debug only

        $.ajax({
            url: url,
            type: 'GET',
            timeout: 7000
        })
        .done(res => {
            if (res && res.status && Array.isArray(res.seats)) {
                resolve(res.seats);
            } else {
                reject(new Error("Invalid seat response"));
            }
        })
        .fail((xhr) => {
            console.error("Seat API Error:", xhr.responseText);
            reject(new Error("Seat API failed"));
        });
    });
}


    // ===================================================================
    // 4. SEAT LOCKING AND UI
    // ===================================================================

    function updateSeatUI(btn, seat, isSelected) {
        const $btn = $(btn);
        if (isSelected) {
            $btn.removeClass("btn-outline-primary bg-engaged").addClass("btn-success bg-selected");
            if (!selectedSeats.includes(seat)) selectedSeats.push(seat);
        } else {
            selectedSeats = selectedSeats.filter(s => s !== seat);
            $btn.removeClass("btn-success bg-selected").addClass("btn-outline-primary");
        }
        calcGrand();
        updateSeatTable(); // 🚨 সিট পরিবর্তন হলে টেবিল আপডেট হবে
    }

    function startLockTimer() {
        if (countdownInterval) clearInterval(countdownInterval);
        if (selectedSeats.length === 0) return stopLockTimer();
        lockExpirationTime = Date.now() + (2 * 60 * 1000);
        countdownInterval = setInterval(updateTimerDisplay, 1000);
        updateTimerDisplay();
    }

    function stopLockTimer() {
        clearInterval(countdownInterval);
        countdownInterval = null;
        lockExpirationTime = null;
        $("#ticket-timer").text("");
    }

    function updateTimerDisplay() {
        if (!lockExpirationTime || selectedSeats.length === 0) return stopLockTimer();
        const remainingTime = lockExpirationTime - Date.now();
        if (remainingTime <= 0) {
            stopLockTimer();
            if (selectedSeats.length > 0) alert("সিট লকের সময় শেষ!");
            selectedSeats = [];
            window.reloadSeats();
            return;
        }
        const m = Math.floor(remainingTime / 60000), s = Math.floor((remainingTime % 60000) / 1000);
        $("#ticket-timer").text(`${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`);
    }

    function syncSeatLocks() {
        if (!TRIP_ID) return;
        $.get(`/admin/seats/locks/${TRIP_ID}`).done(res => {
            if (!res.status || !Array.isArray(res.locks)) return;
            $('.seatBtn.bg-engaged').not('.bg-selected').removeClass('bg-engaged').addClass('btn-outline-primary');
            res.locks.forEach(lock => {
                const $btn = $('[data-seat="' + lock.seat_number + '"]');
                if (!$btn.hasClass('bg-selected')) $btn.removeClass('btn-outline-primary').addClass('bg-engaged');
            });
        });
    }

    window.seatSelect = function(btn) {
        const $btn = $(btn), seat = $btn.data("seat");
        if ($btn.prop('disabled') || !seat || !TRIP_ID) return;
        if ($btn.hasClass("bg-engaged")) { alert("Locked by other counter."); return; }

        if (window.MAX_SEAT_LIMIT && !selectedSeats.includes(seat) && selectedSeats.length >= window.MAX_SEAT_LIMIT) {
            alert("Maximum " + window.MAX_SEAT_LIMIT + " seats allowed.");
            return;
        }

        $btn.prop('disabled', true);
        const requestData = { _token: $("input[name=_token]").val(), schedule_id: TRIP_ID, seat_number: seat };

        if (selectedSeats.includes(seat)) {
            $.post('/admin/seat/release', requestData).done(() => {
                updateSeatUI(btn, seat, false);
                if (selectedSeats.length === 0) stopLockTimer();
            }).always(() => $btn.prop('disabled', false));
        } else {
            $.post('/admin/seat/engage', requestData).done(res => {
                if (res.success) { updateSeatUI(btn, seat, true); startLockTimer(); }
                else alert(res.message || "Lock failed.");
            }).always(() => $btn.prop('disabled', false));
        }
    };


    // ===================================================================
    // 5. TICKET ISSUE & MODALS
    // ===================================================================

    window.confirmTicketIssue = function(mode) {
    // ডাইনামিক টেক্সট সেট করা
    let titleText = (mode == 0) ? "Confirm Booking?" : "Confirm Sale?";
    let alertText = (mode == 0) ? "Do you want to book these seats?" : "Do you want to sell these seats?";
    let confirmBtnText = (mode == 0) ? "Yes!" : "Yes!";
    let confirmBtnColor = (mode == 0) ? '#ffa800' : '#1bc5bd'; // বুকিংয়ের জন্য হলুদ, সেলের জন্য সবুজ

    // ১. সিট নির্বাচন যাচাই
    if (selectedSeats.length === 0) { 
        Swal.fire("Attention!", "Please select at least one seat.", "warning"); 
        return; 
    }

    // ২. যাত্রীর তথ্য যাচাই
    if (!$("#passenger_name").val() || !$("#passenger_mobile").val()) { 
        Swal.fire("Required Info", "Please provide passenger name and mobile number.", "warning"); 
        return; 
    }

    // ৩. কাউন্টার যাচাই
    if (!$("#boarding_counter_id").val() || !$("#dropping_counter_id").val()) { 
        Swal.fire("Missing Counter", "Please select boarding and dropping counters.", "warning"); 
        return; 
    }

    // ৪. SweetAlert2 কনফার্মেশন
    Swal.fire({
        title: titleText,
        text: alertText,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: confirmBtnColor,
        cancelButtonColor: '#f64e60',
        confirmButtonText: confirmBtnText,
        cancelButtonText: "No, Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            // যদি ইউজার 'Yes' দেয় তবে টিকেট ইস্যু হবে
            window.issueTicket(mode);
        }
    });
};

    window.issueTicket = function(mode) {
        const isSelling = (mode === 1);
        const buttonId = `#confirm${mode}`;
        $(buttonId).prop('disabled', true).text("প্রক্রিয়াকরণ...");

        const fare = parseFare();
        const payload = {
            _token: $("input[name=_token]").val(),
            schedule_id: $("#schedule_id").val(),
            journey_date: $("#journey_date_hidden").val(),
            seats: selectedSeats.map(s => ({ seat_number: s, fare: fare })),
            def_counter_id: $("#def_counter_id").val() || null,
            def_counter_master_id: $("#def_counter_master_id").val() || null,
            station_from_to: $("#station_from_to").val(),
            boarding_counter_id: $("#boarding_counter_id").val(),
            dropping_counter_id: $("#dropping_counter_id").val(),
            passenger_name: $("#passenger_name").val(),
            passenger_mobile: $("#passenger_mobile").val(),
            passenger_email: $("#passenger_email").val(),
            passenger_gender: $("#passenger_gender").val() || 'male',
            discount_amount: parseFloat($("#discount-amount").val()) || 0,
            service_charge: parseFloat($("#service_charge").val()) || 0,
            goods_charge: parseFloat($("#goods-charge").val()) || 0,
            callerman_commission: parseFloat($("#callerman-commission").val()) || 0,
            ticket_action: isSelling ? 'sold' : 'booked'
        };

        $.ajax({
            url: "/admin/ticket-issue/store",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
        }).done(res => {
            if (res.status) {
                stopLockTimer();
                selectedSeats = [];
                calcGrand();
                updateSeatTable();
                window.reloadSeats();
                $("#soldTicketModal").modal('show');
            } else alert(res.message);
        }).fail(xhr => {
            alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
        }).always(() => {
            $(buttonId).prop('disabled', false).text(isSelling ? "সিট বিক্রি" : "সিট বুক");
        });
    };

    window.openSoldTicketModal = function(ticketId) {
        $("#soldTicketModalBody").html("<div class='text-center p-3'>Loading...</div>");
        $("#soldTicketModal").modal("show");
        $.get("/admin/ticket-issue/view/" + ticketId, res => {
            if (res.status) $("#soldTicketModalBody").html(res.html);
            else $("#soldTicketModalBody").html(`<div class='alert alert-danger'>${res.message}</div>`);
        });
    };

    window.getPassengerInfoByMobile = function(mobile) {
        if (!mobile || mobile.length < 10) {
            $("#passenger_name").val('');
            $("#discount-amount").val(0.00);
            calcGrand();
            return;
        }
        $.get('/admin/passengers/search', { mobile: mobile }, res => {
            if (res.status && res.name) $("#passenger_name").val(res.name);
        });
        $.get('/admin/passengers/check-loyalty', { mobile: mobile }, res => {
            const disc = parseFloat(res.discount_amount) || 0;
            $("#discount-amount").val(disc.toFixed(2));
            calcGrand();
        });
    };

    window.resetSeatIssueForm = function() {
        $("#passenger_mobile, #passenger_name, #callerman-mobile").val('');
        $("#goods-charge, #discount-amount, #callerman-commission").val(0);
        $("#callerman_mobile_tr").hide();
        selectedSeats = [];
        stopLockTimer();
        calcGrand();
        updateSeatTable(); // টেবিল ক্লিয়ার করুন
        window.reloadSeats();
    };


    // ===================================================================
    // 6. INIT ON LOAD
    // ===================================================================

    $(function() {
        $("#station_from_to").on('change', window.reloadSeats);
        currentFare = parseFare();
        calcGrand();
        updateSeatTable();
        
        if ($("#station_from_to").val() && TRIP_ID) window.reloadSeats();
        setInterval(syncSeatLocks, 30000);

        $('#passenger_mobile').on('keyup', function() {
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => window.getPassengerInfoByMobile($(this).val()), 500);
        });
    });

})(jQuery, window, document);
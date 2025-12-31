<div class="row modal-body">
    <div class="col-md-12">

        <div class="row">

            <div class="col-md-6">
                <table class="table table-sm table-bordered text-left">
                    <tbody>

                        <tr>
                            <td>
                                <i class="fas fa-ticket-alt mr-2"></i>
                                {{-- Use ?? 'N/A' for safety --}}
                                {{ ltrim($ticket->invoice_no ?? 'N/A', 'INV-') }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <i class="fas fa-thumbtack mr-2"></i>
                                {{ $ticket->pnr_no ?? 'N/A' }}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span id="name_view">
                                    <i class="fas fa-user-alt mr-2"></i>
                                    <span id="name_text">{{ $ticket->customer_name ?? 'N/A' }}</span>

                                    <i class="fas fa-edit text-success ml-2" onclick="showNameEditor();"
                                        style="cursor:pointer"></i>
                                </span>

                                <span id="name_edit" style="display:none;">
                                    <input type="text" class="form-control form-control-sm d-inline-block w-50"
                                        id="name_input" value="{{ $ticket->customer_name ?? 'N/A' }}">

                                    <i class="fas fa-save text-success ml-2" onclick="saveNameEditor();"
                                        style="cursor:pointer"></i>
                                    <i class="fas fa-times text-danger ml-2" onclick="cancelNameEditor();"
                                        style="cursor:pointer"></i>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <i class="fas fa-mobile-alt mr-2"></i>
                                {{ $ticket->customer_mobile ?? 'N/A' }}
                                <span class="float-right">
                                    <i class="fas fa-sms text-muted mr-1"></i> 0
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $ticket->passenger_email ?? 'N/A' }}
                                <span class="float-right">
                                    <i class="fas fa-envelope text-muted mr-1"></i> 0
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                {{-- Using PHP 8+ Nullsafe Operator (?->) is safer but requires the ?-> on all deep
                                relations --}}
                                {{ $ticket->fromStation?->name ?? 'N/A' }}
                                <span class="badge badge-success ml-1">
                                    {{ $ticket->boardingCounter?->short_name ?? 'N/A' }}
                                </span>
                                <br>
                                <i class="far fa-clock mr-2"></i>
                                <span class="text-success">
                                    {{-- Using optional() helper or nullsafe operator for nested schedule/bus relations
                                    is safer --}}
                                    {{ date('d M Y h:i A', strtotime($ticket->schedule?->start_time ?? now())) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ $ticket->toStation?->name ?? 'N/A' }}
                                <span class="badge badge-danger ml-1">
                                    {{ $ticket->droppingCounter?->short_name ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <small class="text-muted">
                    ★ Issued by <span class="text-success">{{ $ticket->issuedBy?->name ?? 'N/A' }}</span>
                    {{-- FIXED: Assumed $ticket->issueCounter is a relationship, using nullsafe operator --}}
                    from <span class="text-warning">{{ $ticket->issueCounter?->name ?? 'N/A' }}</span>
                    <br>
                    ★ Issued at <span
                        class="text-info">{{ $ticket->created_at?->format('d-m-Y h:i A') ?? 'N/A' }}</span>
                </small>
            </div>

            <div class="col-md-6">

                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="20%">#</th>
                            <th class="text-center" width="45%">SEAT</th>
                            <th class="text-center" width="35%">ISSUE</th>
                        </tr>
                    </thead>

                    <tbody id="sell-seat-table">
                        {{-- 🚨 FIX: $ticket->seats এর পরিবর্তে $seatList অ্যারে ব্যবহার করা হচ্ছে --}}
                        @forelse ($seatList as $key => $seatNumber)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>

                                <td class="text-center">{{ trim($seatNumber) }}</td>

                                <td class="text-center">
                                    {{-- value-তেও $seatNumber ব্যবহার করা হয়েছে --}}
                                    <input type="checkbox" class="booked-seat-no" data-si="{{ $key }}"
                                        value="{{ trim($seatNumber) }}" checked>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No seats available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">Sub Total :</td>
                            <td class="text-right">{{ number_format($ticket->sub_total ?? 0) }} Tk</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Goods Charge :</td>
                            <td class="text-right">{{ number_format($ticket->goods_charge ?? 0) }} Tk</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Discount :</td>
                            <td class="text-right">{{ number_format($ticket->discount_amount ?? 0) }} Tk</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Callerman Commission :</td>
                            <td class="text-right">{{ number_format($ticket->callerman_commission ?? 0) }} Tk</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="2" class="text-right">Grand Total :</td>
                            <td class="text-right">{{ number_format($ticket->grand_total ?? 0) }} Tk</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

    </div>
</div>

<div class="modal-footer">
    <div class="col-md-12 p-0 m-0">
        <div class="row">
            <div class="col-md-10">
                @php
                    $singleSeatFare = $ticket->seats->first()?->fare ?? 0;
                    $totalSeats = $ticket->seats->count();
                    $seatNumbers = $ticket->seats->pluck('seat_numbers')->implode(',');
                    $invoiceId = ltrim($ticket->invoice_no ?? '0', 'INV-');
                    $tId = $ticket->id ?? 0;
                    $currentStatus = strtolower($ticket->status_label ?? '');
                @endphp

                {{-- Badge এ ID যোগ করা হয়েছে --}}
                <!-- <span id="ticket-status-badge"
                    class="status-label-text badge {{ $ticket->status_label == 'Booked' ? 'badge-warning' : 'badge-success' }}">
                    {{ $ticket->status_label }}
                </span> -->

                @if($currentStatus === 'booked')
                    <button id="confirm-sell-btn" class="btn btn-sm btn-primary btn-pill px-5"
                        onclick="confirmTicketSale('{{ $invoiceId }}', {{ $tId }});">
                        <i class="fas fa-shopping-cart"></i> SELL TICKET
                    </button>
                @endif

                <button class="btn btn-sm btn-info btn-pill px-5" onclick="PrintTicketNonAC();">
                    <i class="far fa-newspaper"></i> PRINT
                </button>

                <button class="btn btn-sm btn-success btn-pill px-5" onclick="TicketSmsSend('{{ $invoiceId }}');">
                    <i class="fas fa-sms"></i> SEND SMS
                </button>

                <button class="btn btn-sm btn-success btn-pill px-5" onclick="TicketemailSend('{{ $invoiceId }}');">
                    <i class="fas fa-envelope"></i> SEND EMAIL
                </button>

                <input type="hidden" id="conf_seat_fare" value="{{ $singleSeatFare }}">
                <input type="hidden" id="conf_total_seat" value="{{ $totalSeats }}">
                <input type="hidden" id="conf_seat_numbers" value="{{ $seatNumbers }}">
                <input type="hidden" id="conf_goods_charge" value="{{ $ticket->goods_charge ?? 0 }}">
                <input type="hidden" id="conf_discount_amount" value="{{ $ticket->discount_amount ?? 0 }}">
                <input type="hidden" id="conf_callerman_commission" value="{{ $ticket->callerman_commission ?? 0 }}">

                @can('Can Issue Ticket')
                    <button class="btn btn-sm btn-danger btn-pill px-5" onclick="cancelTicket({{ $tId }})">
                        <i class="fas fa-times"></i> CANCEL TICKET
                    </button>
                @endcan
            </div>

            <div class="col-md-2 text-right">
                <button class="btn btn-sm btn-secondary btn-pill" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<script>
    // আপনার Blade View-তে <script> ট্যাগ অথবা আপনার main.js ফাইলে এই কোডটি রাখুন।

    function PrintTicketNonAC() {
        // Hidden Input field থেকে ডেটা সংগ্রহ করা
        const ticketData = {
            invoice_no: '{{ ltrim($ticket->invoice_no ?? 'N/A', 'INV-') }}',
            pnr_no: $('#conf_pnr_no').val(),
            customer_name: $('#conf_customer_name').val(),
            mobile: '{{ $ticket->customer_mobile ?? 'N/A' }}',
            schedule_time: '{{ date('d M Y h:i A', strtotime($ticket->schedule?->start_time ?? now())) }}',

            // সিট এবং কাউন্টার ডেটা
            seat_numbers: $('#conf_seat_numbers').val(),
            total_seats: $('#conf_total_seat').val(),
            boarding_point: $('#conf_boarding').val(),
            dropping_point: $('#conf_dropping').val(),

            // ফিন্যান্সিয়াল ডেটা
            grand_total: $('#conf_grand_total').val(),
            sub_total: '{{ $ticket->sub_total ?? 0 }}',
            discount: $('#conf_discount_amount').val(),
            commission: $('#conf_callerman_commission').val(),
            goods_charge: $('#conf_goods_charge').val(),
        };

        // ----------------------------------------------------
        // 🚨 প্রিন্টিং লজিক (সাধারণত এটি একটি নতুন উইন্ডো বা AJAX প্রিন্ট কল হয়)
        // ----------------------------------------------------

        // ১. AJAX এর মাধ্যমে সার্ভারে ডেটা পাঠিয়ে প্রিন্ট করার অনুরোধ:
        $.ajax({
            url: '/admin/print/non-ac-ticket', // আপনার প্রিন্টিং রাউট
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ticket_id: '{{ $ticket->id ?? 0 }}',
                data: ticketData
            },
            success: function (response) {
                // যদি সার্ভার প্রিন্ট ডেটা পাঠায়, নতুন উইন্ডো খুলুন
                const printWindow = window.open('', '_blank');
                printWindow.document.write(response.html);
                printWindow.document.close();
                printWindow.print();
            },
            error: function () {
                alert('Failed to generate print data.');
            }
        });


        // ২. বিকল্প: শুধু ডেটা কনসোলে দেখানো (ডিবাগিং এর জন্য)
        console.log("Preparing to Print Ticket (Non-AC):", ticketData);
        alert("Print functionality is pending. Check console for data.");
    }

    const SMS_ROUTE_URL = '{{ route('admin.ticket.send_sms', ['invoiceId' => 'INVOICE_ID']) }}';
    // const CANCEL_ROUTE_URL = '{{ route('admin.ticket.cancel.selective', ['id' => 'TICKET_ID']) }}'; // Include if needed by other functions

    function TicketSmsSend(invoiceId) {
        if (!confirm('Are you sure you want to send the SMS for Invoice: ' + invoiceId + '?')) {
            return;
        }

        // 🚨 FIX 2: Correctly replace the placeholder with the actual invoiceId
        const finalSmsUrl = SMS_ROUTE_URL.replace('INVOICE_ID', invoiceId);

        $.ajax({
            url: finalSmsUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                invoice_id: invoiceId
            },
            beforeSend: function () {
                // Show loading spinner
                $('.btn-success i.fa-sms').removeClass('fa-sms').addClass('fa-spinner fa-spin');
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errorMsg = 'Failed to send SMS.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Use response message from controller if available
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function () {
                // Remove loading spinner
                $('.btn-success i.fa-spinner').removeClass('fa-spinner fa-spin').addClass('fa-sms');
            }
        });
    }

    const EMAIL_ROUTE_URL = '{{ route('admin.ticket.send_email', ['invoiceId' => 'INVOICE_ID']) }}';

    // 🚨 FIX: Define the function in the global scope
    function TicketemailSend(invoiceId) {
        if (!confirm('Are you sure you want to email the ticket for Invoice: ' + invoiceId + '?')) {
            return;
        }

        const finalEmailUrl = EMAIL_ROUTE_URL.replace('INVOICE_ID', invoiceId);

        $.ajax({
            url: finalEmailUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                invoice_id: invoiceId
            },
            beforeSend: function () {
                // Show loading spinner
                $('.btn-success i.fa-envelope').removeClass('fa-envelope').addClass('fa-spinner fa-spin');
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errorMsg = 'Failed to process email request.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function () {
                // Remove loading spinner
                $('.btn-success i.fa-spinner').removeClass('fa-spinner fa-spin').addClass('fa-envelope');
            }
        });
    }

    function confirmTicketSale(invoiceId, ticketId) {
        // ১. SweetAlert2 কনফার্মেশন বক্স
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure you want to issue this ticket",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1BC5BD',
            cancelButtonColor: '#181C32',
            confirmButtonText: 'Yes, ',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('admin.ticket.convertToSale') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ticket_id: ticketId
                    },
                    beforeSend: function () {
                        // বাটন ডিজেবল এবং প্রসেসিং দেখানো
                        $('#confirm-sell-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                    },
                    success: function (response) {
                        if (response.status) {
                            // ২. SweetAlert2 সাকসেস মেসেজ
                            Swal.fire({
                                title: 'সফল!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#1bc5bd',
                            });

                            // ৩. UI আপডেট (বাটন হাইড এবং ব্যাজ পরিবর্তন)
                            $('#confirm-sell-btn').fadeOut();
                            $('#ticket-status-badge').text('Sold').removeClass('badge-warning').addClass('badge-success');

                            // ৪. সীট লেআউট রিলোড করা (পেজ রিলোড ছাড়া)
                            if (typeof window.reloadSeats === "function") {
                                window.reloadSeats();

                            } else {
                                // যদি নির্দিষ্ট ফাংশন না থাকে তবে মডালটি বন্ধ করে মেইন সিট প্ল্যান আপডেট করুন
                                $('#ticketDetailModal').modal('hide');
                                // আপনার প্রজেক্টের মেইন সিট ফিল্টার বা লোডার ফাংশনটি এখানে দিন
                                $('.filter-submit-btn').click();
                            }

                        } else {
                            Swal.fire('এরর!', response.message, 'error');
                            $('#confirm-sell-btn').prop('disabled', false).html('<i class="fas fa-shopping-cart"></i> SELL TICKET');
                        }
                    },
                    error: function () {
                        Swal.fire('এরর!', 'সার্ভারের সাথে যোগাযোগ করা সম্ভব হয়নি।', 'error');
                        $('#confirm-sell-btn').prop('disabled', false).html('<i class="fas fa-shopping-cart"></i> SELL TICKET');
                    }
                });
            }
        });
    }
    const getRawVal = (id) => parseFloat(document.getElementById(id).innerText.replace(/,/g, '')) || 0;

    // মডাল ওপেন হওয়ার সময়কার আসল সাব-টোটাল
    let originalSubTotal = getRawVal('sub-total');
    const goodsCharge = getRawVal('goods-charge');
    const discount = getRawVal('discount');
    const commission = parseFloat("{{ $ticket->callerman_commission ?? 0 }}") || 0;

    // চেকবক্স চ্যাঞ্জ হলে রিয়েল-টাইম গ্র্যান্ড টোটাল আপডেট
    $(document).on('change', '.booked-seat-no', function () {
        let totalRefundAmount = 0;

        // যে সিটগুলোতে টিক (Check) দেওয়া হয়েছে সেগুলোর মোট দাম
        $('.booked-seat-no:checked').each(function () {
            totalRefundAmount += parseFloat($(this).data('price')) || 0;
        });

        // নতুন সাব-টোটাল = অরিজিনাল - সিলেক্ট করা সিটের দাম
        let newSubTotal = originalSubTotal - totalRefundAmount;
        let newGrandTotal = newSubTotal + goodsCharge - discount - commission;

        // UI আপডেট
        document.getElementById('sub-total').innerText = newSubTotal.toLocaleString();
        document.getElementById('grand-total').innerText = newGrandTotal.toLocaleString();
    });
</script>
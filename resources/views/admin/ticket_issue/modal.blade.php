<!-- Ticket Issue Modal -->
<div class="modal fade" id="ticketIssueModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <span id="modalRouteText">Dhaka → Chittagong</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="background:#e8ffff;">
                <div class="container-fluid">

                    <div class="row">

                        <!-- Left: Seat Layout -->
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="fw-bold">Coach: <span id="tripCoach"></span></h5>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <b>Departure:</b>
                                        <div id="tripDeparture" class="text-danger small"></div>
                                    </div>

                                    <div>
                                        <b>Arrival:</b>
                                        <div id="tripArrival" class="text-success small"></div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Seat Layout -->
                                <div class="seat-layout p-2" style="background:white; border-radius:8px;">
                                    <div class="row text-center">

                                        @for($r = 1; $r <= 9; $r++)
                                            <div class="col-4 p-1">
                                                A{{ $r }}
                                            </div>
                                            <div class="col-4 p-1">
                                                C{{ $r }}
                                            </div>
                                            <div class="col-4 p-1">
                                                D{{ $r }}
                                            </div>
                                        @endfor

                                    </div>
                                </div>

                                <br>

                                <div class="text-center">
                                    <button class="btn btn-info w-50">Refresh</button>
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Fare Table -->
                        <div class="col-md-4">
                            <table class="table table-bordered small">
                                <thead>
                                    <tr class="table-success">
                                        <th>#</th>
                                        <th>Seat</th>
                                        <th>Type</th>
                                        <th>Fare</th>
                                    </tr>
                                </thead>
                                <tbody id="fareTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">No seats selected</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table">
                                <tr>
                                    <th>Sub Total:</th>
                                    <td id="subTotal">0</td>
                                </tr>

                                <tr>
                                    <th>Goods Charge:</th>
                                    <td><input type="number" class="form-control"></td>
                                </tr>

                                <tr>
                                    <th>Discount:</th>
                                    <td><input type="number" class="form-control"></td>
                                </tr>

                                <tr>
                                    <th>Callerman Commission:</th>
                                    <td><input type="number" class="form-control"></td>
                                </tr>

                                <tr class="table-success">
                                    <th>Grand Total:</th>
                                    <th id="grandTotal">0</th>
                                </tr>
                            </table>

                        </div>

                        <!-- Right: Passenger Info -->
                        <div class="col-md-4">
                            <div class="card p-3">

                                <div class="mb-2">
                                    <label>Passenger Mobile Number</label>
                                    <input type="text" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label>Passenger Full Name</label>
                                    <input type="text" class="form-control">
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label>Gender</label>
                                        <select class="form-control">
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>

                                    <div class="col-6 mb-2">
                                        <label>Nationality</label>
                                        <select class="form-control">
                                            <option>Bangladesh</option>
                                            <option>India</option>
                                            <option>Nepal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>

                                    <div class="col-6 mb-2">
                                        <input type="text" class="form-control" placeholder="Passport Number">
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input">
                                    <label class="form-check-label">VIP Passenger</label>
                                </div>

                                <hr>

                                <button class="btn btn-primary w-100">Trip Sheet</button>
                                <button class="btn btn-secondary w-100 mt-2">Reset</button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
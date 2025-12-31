@<div class="station-counter-wrapper">
    @foreach($stations as $station)
        <div class="card station-card mb-3 p-3" data-station-id="{{ $station->id }}">
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-info me-2">{{ $loop->iteration }}</span>
                <strong class="flex-grow-1">{{ $station->name }}</strong>
                <button type="button" class="btn btn-sm btn-success add-counter-btn">
                    <i class="fas fa-plus"></i> Add Counter
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0 counter-table">
                    <thead class="table-light">
                        <tr>
                            <th>Counter Name</th>
                            <th>From Time</th>
                            <th>To Time</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($station->counters) && count($station->counters) > 0)
                            @foreach($station->counters as $counter)
                                <tr>
                                    <td>{{ $counter->name }}</td>
                                    <td>{{ $counter->from_time ?? '-' }}</td>
                                    <td>{{ $counter->to_time ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="empty-row">
                                <td colspan="4" class="text-center text-muted">No counters for this station.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
<script>
    document.addEventListener("click", async function (e) {
        if (e.target.closest(".add-counter-btn")) {
            const btn = e.target.closest(".add-counter-btn");
            const stationCard = btn.closest(".station-card");
            const stationId = stationCard.getAttribute("data-station-id");
            const tableBody = stationCard.querySelector("tbody");

            // Input prompt
            const name = prompt("Enter Counter Name:");
            if (!name) return;

            const from = prompt("From Time (e.g., 08:00 AM):");
            const to = prompt("To Time (e.g., 10:00 PM):");

            try {
                const res = await fetch(`/admin/stations/${stationId}/counters`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name, from_time: from, to_time: to })
                });

                const data = await res.json();
                if (data.success) {
                    if (tableBody.querySelector(".empty-row")) {
                        tableBody.innerHTML = "";
                    }

                    const row = document.createElement("tr");
                    row.innerHTML = `
                    <td>${data.counter.name}</td>
                    <td>${data.counter.from_time ?? "-"}</td>
                    <td>${data.counter.to_time ?? "-"}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger delete-counter" data-id="${data.counter.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                    tableBody.appendChild(row);
                } else {
                    alert("Failed to add counter");
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Something went wrong");
            }
        }
    });
</script>

{{-- ✅ STYLE --}}
<style>
    .station-counter-wrapper {
        background: #f8fafc;
        padding: 20px;
        border-radius: 10px;
    }

    .station-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }

    .station-card:hover {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .station-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f1f5f9;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .station-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .station-number {
        background: #8b5cf6;
        color: white;
        border-radius: 8px;
        padding: 2px 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .station-name {
        font-weight: 600;
        font-size: 16px;
        color: #1e293b;
    }

    .btn-add {
        background: #06b6d4;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: background 0.2s;
    }

    .btn-add:hover {
        background: #0891b2;
    }

    .counter-table-wrapper {
        padding: 12px 16px;
    }

    .counter-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .counter-table thead th {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
        padding: 8px;
        font-weight: 600;
        color: #475569;
    }

    .counter-table td {
        padding: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .counter-table input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 13px;
    }

    .text-center {
        text-align: center;
    }

    .btn-action {
        border: none;
        border-radius: 6px;
        padding: 4px 6px;
        margin: 0 3px;
        cursor: pointer;
        font-size: 13px;
    }

    .btn-save {
        background: #16a34a;
        color: white;
    }

    .btn-save:hover {
        background: #15803d;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .btn-delete:hover {
        background: #dc2626;
    }

    @media (max-width: 768px) {
        .station-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .btn-add {
            align-self: flex-end;
        }
    }
</style>
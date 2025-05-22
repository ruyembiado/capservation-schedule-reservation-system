@extends('layouts.app') <!-- Extend the main layout -->
@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Transactions</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            @if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'instructor')
                <form action="{{ url()->current() }}" method="GET" class="mb-4">
                    <div class="filter-container d-flex gap-2 align-items-end">
                        <div class="program-filter d-flex align-items-center gap-2">
                            <label for="program" class="form-label">Program: </label>
                            <select id="program" class="form-control select2" name="program">
                                <option value="">-- Select Program --</option>
                                <option value="BSIT" {{ request('program') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSCS" {{ request('program') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                                <option value="BSIS" {{ request('program') == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                            </select>
                        </div>

                        <div class="status-filter d-flex align-items-center gap-2">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-control select2" name="status">
                                <option value="">-- Select Status --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group Name</th>
                            {{-- <th>Members</th> --}}
                            <th>Program</th>
                            <th>Type of Defense</th>
                            <th>Status</th>
                            <th>Date</th>
                            {{-- <th>Time</th> --}}
                            <th>Transaction Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::ucfirst($transaction->group->username) }}</td>
                                {{-- <td>
                                    @foreach (json_decode($transaction->members, true) as $member)
                                        <li>{{ $member }}</li>
                                    @endforeach
                                </td> --}}
                                <td>{{ $transaction->program }}</td>
                                <td>{{ Str::title(str_replace('_', ' ', $transaction->type_of_defense)) }}</td>
                                <td>
                                    @php $bg = ''; @endphp
                                    @switch($transaction->status)
                                        @case('paid')
                                            @php $bg = 'bg-success'; @endphp
                                        @break

                                        @case('pending')
                                            @php $bg = 'bg-warning'; @endphp
                                        @break

                                        @default
                                            @php $bg = ''; @endphp
                                    @endswitch
                                    <li>
                                        <span
                                            class="badge {{ $bg }}">{{ Str::ucfirst($transaction->status) }}</span>
                                    </li>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d') }}</td>
                                {{-- <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}</td> --}}
                                <td>{{ $transaction->transaction_code }}</td>
                                <td>
                                    @if (auth()->user()->user_type == 'admin')
                                        @if ($transaction->status === 'pending')
                                            <form action="{{ route('transaction.update', $transaction->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Mark as Paid
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    @if (auth()->user()->user_type == 'student')
                                        @if (!$transaction->proof_file)
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#uploadModal"
                                                onclick="setTransactionId({{ $transaction->id }})">
                                                <i class="fas fa-upload"></i> Upload Proof of Payment
                                            </button>
                                        @endif
                                    @endif
                                    @if ($transaction->proof_file)
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#proofModal{{ $transaction->id }}">
                                            View Proof
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="proofModal{{ $transaction->id }}" tabindex="-1"
                                            aria-labelledby="proofModalLabel{{ $transaction->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="proofModalLabel{{ $transaction->id }}">
                                                            Proof of Payment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset($transaction->proof_file) }}"
                                                            alt="Proof of Payment" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->

    <!-- Upload Proof of Payment Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transaction.upload_proof') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="transaction_id" id="transactionIdInput">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Proof of Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="proof" class="form-label">Choose file</label>
                            <input type="file" class="form-control" name="proof" id="proof" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection <!-- End the content section -->

<script>
    function setTransactionId(id) {
        document.getElementById('transactionIdInput').value = id;
    }
</script>

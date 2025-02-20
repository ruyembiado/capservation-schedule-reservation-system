@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Transactions</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group Name</th>
                            <th>Members</th>
                            <th>Program</th>
                            <th>Type of Defense</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Transaction Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::ucfirst($transaction->group->username) }}</td>
                                <td>
                                    @foreach (json_decode($transaction->members, true) as $member)
                                        <li>{{ $member }}</li>
                                    @endforeach
                                </td>
                                <td>{{ $transaction->program }}</td>
                                <td>{{ Str::title(str_replace('_', ' ', $transaction->type_of_defense)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->date_created)->format('Y-m-d') }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->date_created)->format('h:i A') }}</td>
                                <td>{{ $transaction->transaction_code }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

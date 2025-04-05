@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Notifications</h1>
    </div>

    <!-- Tabs for Notifications -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="system-alert-tab" data-bs-toggle="tab" href="#system-alert" role="tab"
                        aria-controls="system-alert" aria-selected="true">System Alerts</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="update-status-tab" data-bs-toggle="tab" href="#update-status" role="tab"
                        aria-controls="update-status" aria-selected="false">Update Status</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="reminders-tab" data-bs-toggle="tab" href="#read" role="tab"
                        aria-controls="read" aria-selected="false">Reminders</a>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="notificationTabsContent">
                <div class="tab-pane fade show active" id="system-alert" role="tabpanel" aria-labelledby="system-alert-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Notification Title</th>
                                    <th>Notification Message</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($notifications->where('notification_type', 'system_alert')->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endif
                                @foreach ($notifications->where('notification_type', 'system_alert') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->create_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="update-status" role="tabpanel" aria-labelledby="update-status-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User Type</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($notifications->where('notification_type', 'status_update')->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endif
                                @foreach ($notifications->where('notification_type', 'status_update') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->create_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="read" role="tabpanel" aria-labelledby="reminders-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User Type</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($notifications->where('notification_type', 'reminders')->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endif
                                @foreach ($notifications->where('notification_type', 'reminders') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->create_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

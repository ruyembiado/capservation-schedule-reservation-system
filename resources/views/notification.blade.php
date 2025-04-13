@extends('layouts.app')

@section('content')
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
                <!-- System Alerts Tab -->
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
                                @forelse ($notifications->where('notification_type', 'system_alert') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Update Status Tab -->
                <div class="tab-pane fade" id="update-status" role="tabpanel" aria-labelledby="update-status-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
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
                                @forelse ($notifications->where('notification_type', 'status_update') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reminders Tab -->
                <div class="tab-pane fade" id="read" role="tabpanel" aria-labelledby="reminders-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable3" width="100%" cellspacing="0">
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
                                @forelse ($notifications->where('notification_type', 'reminder') as $notification)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $notification->notification_title }}</td>
                                        <td>{{ $notification->notification_message }}</td>
                                        <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>
                                            <a href="/reservation/{{ $notification->_link_id }}"
                                                class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
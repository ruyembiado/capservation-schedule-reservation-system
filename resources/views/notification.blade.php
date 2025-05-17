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
                    <a class="nav-link" id="reminders-tab" data-bs-toggle="tab" href="#reminders" role="tab"
                        aria-controls="reminders" aria-selected="false">Reminders</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="custom-reminder-tab" data-bs-toggle="tab" href="#custom-reminder" role="tab"
                        aria-controls="custom-reminder" aria-selected="false">Custom Reminders</a>
                </li>
                @if (auth()->user()->user_type == 'admin')
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="custom-reminder-tab" data-bs-toggle="tab" href="#create-reminder"
                            role="tab" aria-controls="create-reminder" aria-selected="false">Create Custom Reminder</a>
                    </li>
                @endif
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
                                @foreach ($notifications->where('notification_type', 'system_alert') as $notification)
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
                                @endforeach
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
                                @foreach ($notifications->where('notification_type', 'update_status') as $notification)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reminders Tab -->
                <div class="tab-pane fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
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
                                @foreach ($notifications->where('notification_type', 'reminder') as $notification)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom Reminders Tab -->
                <div class="tab-pane fade" id="custom-reminder" role="tabpanel" aria-labelledby="custom-reminder-tab">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="dataTable4" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Title Status</th>
                                    <th>Message</th>
                                    <th>Recipient</th>
                                    <th>Defense Stage</th>
                                    <th>Schedule Date & Time</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customReminders as $customReminder)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $customReminder->title_status }}</td>
                                        <td>{{ $customReminder->message }}</td>
                                        <td>
                                            {{ $customReminder->group->username ?? 'Unknown' }}
                                        </td>
                                        <td>{{ $customReminder->defense_stage ?? '—' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($customReminder->schedule_datetime)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($customReminder->created_at)->format('Y-m-d h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Create Custom Reminders Tab -->
                <div class="tab-pane fade" id="create-reminder" role="tabpanel" aria-labelledby="custom-reminders-tab">
                    <form action="{{ route('custom.reminder') }}" method="POST"
                        class="col-12 col-md-8 col-xl-4 m-auto py-5">
                        @csrf
                        <div class="mb-3">
                            <label for="title_status" class="form-label">Title Status</label>
                            <input type="text" name="title_status" id="title_status" class="form-control" required>
                            @error('title_status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="recipient" class="form-label">Recipient</label>
                            <select name="group_id" id="reserve_group" class="form-select select2" required>
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ ucfirst($group->username) }}</option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="defense_stage" class="form-label">Defense Stage (Optional)</label>
                            <input type="text" name="defense_stage" id="defense_stage" class="form-control">
                            @error('defense_stage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="schedule_datetime" class="form-label">Schedule Date & Time</label>
                            <input type="datetime-local" name="schedule_datetime" id="schedule_datetime"
                                class="form-control" required>
                            @error('schedule_datetime')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Create Reminder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

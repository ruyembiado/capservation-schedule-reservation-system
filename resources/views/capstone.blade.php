@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Capstones</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group</th>
                            <th>Titles</th>
                            <th>Title Status</th>
                            <th>Capstone Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($capstones as $groupId => $groupCapstones)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::ucfirst($groupCapstones->first()->user->username) }}</td>
                                <td>
                                    @foreach ($groupCapstones as $capstone)
                                        <li><i class="fa fa-book"></i> {{ $capstone->title }}</li>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($groupCapstones as $capstone)
                                        @php $bg = ''; @endphp
                                        @switch($capstone->title_status)
                                            @case('defended')
                                                @php $bg = 'bg-success'; @endphp
                                            @break

                                            @case('pending')
                                                @php $bg = 'bg-warning'; @endphp
                                            @break

                                            @case('rejected')
                                                @php $bg = 'bg-danger'; @endphp
                                            @break

                                            @default
                                                @php $bg = ''; @endphp
                                        @endswitch

                                        <li>
                                            <span
                                                class="badge {{ $bg }}">{{ Str::title(Str::replace('_', ' ', $capstone->title_status)) }}</span>
                                        </li>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($groupCapstones as $capstone)
                                        <li>{{ Str::title(Str::replace('_', ' ', $capstone->capstone_status)) }}</li>
                                    @endforeach
                                </td>
                                <td>{{ $groupCapstones->first()->created_at->format('Y-m-d h:i A') }}</td>
                                <td>
                                    @if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'instructor')
                                        @php
                                            $filteredCapstones = $groupCapstones->filter(function ($capstone) {
                                                return $capstone->capstone_status !== 'title_defense';
                                            });
                                        @endphp

                                        @if ($filteredCapstones->isNotEmpty())
                                            <a href="/update_capstone/{{ implode('/', $filteredCapstones->pluck('id')->toArray()) }}"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                        @else
                                            <a href="/update_capstone/{{ implode('/', $groupCapstones->pluck('id')->toArray()) }}"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                        @endif
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

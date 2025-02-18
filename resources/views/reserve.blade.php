@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Reserve</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="history-container col-6 text-center m-auto">
                <ul class="base-timeline m-auto p-0">
                    <li class="base-timeline__item {{ empty($reservation) ? 'base-timeline__item--active' : '' }}"></li>
                    <li class="base-timeline__item">
                    </li>
                    <li class="base-timeline__item">
                    </li>
                </ul>
            </div>
            @if (empty($reservation))
                <div class="form-container m-auto mt-3 col-6">
                    <div class="text-center">
                        <span>Your reservation is now set for the first stage of your Capstone Defense</span>
                        <h2>TITLE DEFENSE</h2>
                    </div>
                    <p class="mt-3 mb-2">Input your three titles for checking</p>
                    <form action="" method="POST">
                        @csrf
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control @error('title_1') is-invalid @enderror" name="title_1"
                                placeholder="Title 1">
                            @error('title_1')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control @error('title_2') is-invalid @enderror" name="title_2"
                                placeholder="Title 2">
                            @error('title_2')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <input type="text" class="form-control @error('title_3') is-invalid @enderror" name="title_3"
                                placeholder="Title 3">
                            @error('title_3')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="instructor" class="form-label">Group</label>
                            <select class="form-control text-center @error('instructor') is-invalid @enderror" name=""
                                id="">
                                <option value="">-- Select Group --</option>
                            </select>
                            @error('instructor')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <button class="btn w-100 bg-theme-primary text-light"
                                onmouseover="this.style.backgroundColor='#0056b3'"
                                onmouseout="this.style.backgroundColor='#012D6C'" type="submit">RESERVE</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

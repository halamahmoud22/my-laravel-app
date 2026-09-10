@extends('layouts.metronic')

@section('content')
<div class="app-main flex-column flex-row-fluid">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Header -->
        <div id="kt_app_header" class="app-header">
            <div class="app-container container-xxl d-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Profile Settings
                    </h1>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="app-container container-xxl">
                <div class="row g-6">
                    <!-- Main Settings -->
                    <div class="col-lg-8">
                        <!-- Update Profile Information -->
                        @include('profile.partials.update-profile-information-form')

                        <!-- Update Password -->
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Danger Zone -->
                    <div class="col-lg-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

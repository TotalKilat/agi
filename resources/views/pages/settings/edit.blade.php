@extends('layouts.app')

@section('title', 'Application Settings')
@section('page-title', 'Application Settings')

@section('content')
    <div class="page-section active">
        <div class="page-container">

            <div class="page-header">
                <div>
                    <h1 class="page-header-title">Application Settings</h1>
                    <p class="page-header-subtitle">Configure your application name, branding, and identity</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" class="card form-card" enctype="multipart/form-data"
                id="settingsForm">
                @csrf
                @method('PUT')

                @include('pages.settings._form')

                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>

        </div>
    </div>
@endsection

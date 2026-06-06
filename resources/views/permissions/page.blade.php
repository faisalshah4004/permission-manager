@extends('permission-manager::layouts.app')
@section('pageTitle', 'Permissions')
@section('activeMenu', 'permissions')
@section('content')
    @livewire('pm-permission-manager')
@endsection

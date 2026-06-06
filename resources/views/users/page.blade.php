@extends('permission-manager::layouts.app')
@section('pageTitle', 'User Roles')
@section('activeMenu', 'users')
@section('content')
    @livewire('pm-user-role-manager')
@endsection

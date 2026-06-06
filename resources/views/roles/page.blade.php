@extends('permission-manager::layouts.app')
@section('pageTitle', 'Roles')
@section('activeMenu', 'roles')
@section('content')
    @livewire('pm-role-manager')
@endsection

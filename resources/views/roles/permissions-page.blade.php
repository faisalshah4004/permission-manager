@extends('permission-manager::layouts.app')
@section('pageTitle', 'Permissions — ' . $roleModel->name)
@section('activeMenu', 'roles')
@section('content')
    @livewire('pm-role-permission-manager', ['roleId' => $roleModel->id])
@endsection

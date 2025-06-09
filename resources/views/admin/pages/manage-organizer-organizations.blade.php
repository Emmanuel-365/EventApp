@extends('admin.connected-base')

@section('title',"MANAGE ORGANIZER'S ORGANISATIONS")

@section('content')

    @livewire('admin.manage-organizations.admin-organizations-list' , ['organizer' => $organizer])

@endsection

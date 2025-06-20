@extends('employee.connected-base')

@section('title', 'Gérer les Événements')

@section('content')
    @livewire('employee.manage-events.list-events')


    @livewire('employee.manage-events.view-event-details')

    @livewire('employee.manage-events.create-event') -
@endsection

@extends('layouts.vertical', ['title' => 'Roles y Permisos'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Administración',
        'title' => 'Roles y Permisos',
    ])

    <livewire:sata.role-manager />
@endsection

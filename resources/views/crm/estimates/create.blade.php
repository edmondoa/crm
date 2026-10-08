@extends('layouts.admin')

@section('title', 'New Estimate')

@section('page-title', 'New Estimate')

@section('content')

<form
    method="POST"
    action="{{ route('crm.estimates.store') }}"
    id="estimateForm"
>

    @csrf

    @include('crm.estimates._form')

</form>

@endsection
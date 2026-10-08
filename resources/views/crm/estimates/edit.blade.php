    @extends('layouts.admin')

@section('title', 'Edit Estimate')

@section('page-title', 'Edit Estimate')

@section('content')

<form
    method="POST"
    action="{{ route('crm.estimates.update', $estimate) }}"
    id="estimateForm"
>

    @csrf
    @method('PUT')

    @include('crm.estimates._form')

</form>

@endsection
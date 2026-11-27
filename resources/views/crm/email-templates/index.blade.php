@extends('layouts.admin')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h1 class="h3 mb-1">Email Templates</h1><p class="text-muted mb-0">Create reusable messages for CRM communications.</p></div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('crm.email-campaigns.index') }}" class="btn btn-outline-primary"><i class="bi bi-megaphone me-1"></i> Manage campaigns</a>
            <a href="{{ route('crm.email-templates.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New template</a>
        </div>
    </div>
    @include('crm.email-templates.partials.alerts')
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, subject, category"></div>
            <div class="col-md-3"><select name="status" class="form-select"><option value="">All statuses</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div>
            <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button><a href="{{ route('crm.email-templates.index') }}" class="btn btn-link">Reset</a></div>
        </form>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Template</th><th>Category</th><th>Subject</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse($templates as $template)
            <tr><td><div class="fw-semibold">{{ $template->name }}</div><small class="text-muted">Updated {{ $template->updated_at?->diffForHumans() }}</small></td><td><span class="badge text-bg-light">{{ ucfirst($template->category) }}</span></td><td>{{ Str::limit($template->subject, 65) }}</td><td><span class="badge {{ $template->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $template->status ? 'Active' : 'Inactive' }}</span></td><td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('crm.email-templates.preview', $template) }}" title="Preview"><i class="bi bi-eye"></i></a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('crm.email-templates.edit', $template) }}" title="Edit"><i class="bi bi-pencil"></i></a>
                <form class="d-inline" method="POST" action="{{ route('crm.email-templates.duplicate', $template) }}">@csrf<button class="btn btn-sm btn-outline-secondary" title="Duplicate"><i class="bi bi-copy"></i></button></form>
                <form class="d-inline" method="POST" action="{{ route('crm.email-templates.destroy', $template) }}" onsubmit="return confirm('Delete this email template?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button></form>
            </td></tr>
        @empty <tr><td colspan="5" class="text-center py-5"><i class="bi bi-envelope-paper fs-2 text-muted"></i><p class="fw-semibold mt-2 mb-1">No email templates yet</p><p class="text-muted">Create your first reusable email template.</p><a class="btn btn-primary" href="{{ route('crm.email-templates.create') }}">Create template</a></td></tr> @endforelse
        </tbody></table></div>{{ $templates->links() }}
    </div></div>
</div>
@endsection

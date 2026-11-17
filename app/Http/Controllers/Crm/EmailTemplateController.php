<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Mail\TemplateTestMail;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Throwable;

class EmailTemplateController extends Controller
{
    public function __construct(private EmailTemplateRenderer $renderer) {}

    public function index(Request $request): View
    {
        $templates = EmailTemplate::query()
            ->when($request->filled('q'), fn ($q) => $q->where(function ($sub) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $sub->where('name', 'like', $term)->orWhere('subject', 'like', $term)->orWhere('category', 'like', $term);
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status === 'active'))
            ->latest()->paginate(15)->withQueryString();

        return view('crm.email-templates.index', compact('templates'));
    }

    public function create(): View
    {
        $template = new EmailTemplate(['status' => true, 'category' => 'general']);
        return view('crm.email-templates.create', $this->formData($template));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;
        $template = EmailTemplate::create($data);

        return redirect()->route('crm.email-templates.edit', $template)->with('success', 'Email template created.');
    }

    public function edit(EmailTemplate $email_template): View
    {
        return view('crm.email-templates.edit', $this->formData($email_template));
    }

    public function update(Request $request, EmailTemplate $email_template): RedirectResponse
    {
        $data = $this->validated($request);
        $data['updated_by'] = $request->user()?->id;
        $email_template->update($data);

        return redirect()->route('crm.email-templates.edit', $email_template)->with('success', 'Email template updated.');
    }

    public function duplicate(EmailTemplate $email_template): RedirectResponse
    {
        $copy = $email_template->replicate();
        $copy->name = $email_template->name.' (Copy)';
        $copy->slug = $this->uniqueSlug($copy->name);
        $copy->status = false;
        $copy->created_by = auth()->id();
        $copy->updated_by = auth()->id();
        $copy->save();

        return redirect()->route('crm.email-templates.edit', $copy)->with('success', 'Template duplicated. Review it before activating.');
    }

    public function destroy(EmailTemplate $email_template): RedirectResponse
    {
        $email_template->delete();
        return redirect()->route('crm.email-templates.index')->with('success', 'Email template deleted.');
    }

    public function preview(Request $request, EmailTemplate $email_template): View
    {
        $data = $this->previewData($request);
        return view('crm.email-templates.preview', [
            'template' => $email_template,
            'subject' => $this->renderer->renderSubject($email_template->subject, $data),
            'html' => $this->renderer->render($email_template->body_html, $data),
        ]);
    }

    public function testEmail(Request $request, EmailTemplate $email_template): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email:rfc', 'max:254'],
            'sample_data' => ['nullable', 'array'],
        ]);

        if (!config('mail.default') || config('mail.default') === 'log') {
            return back()->withErrors(['test_email' => 'Configure a real mail transport in your .env before sending a test email.']);
        }

        $data = $this->previewData($request);
        $subject = $this->renderer->renderSubject($email_template->subject, $data);
        $html = $this->renderer->render($email_template->body_html, $data);

        try {
            Mail::to($validated['test_email'])->send(new TemplateTestMail($subject, $html));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['test_email' => 'Test email could not be sent. Check your mail configuration and application logs.']);
        }

        return back()->with('success', 'Test email sent to '.$validated['test_email'].'.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string', 'max:200000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'boolean'],
        ]) + ['status' => $request->boolean('status')];
    }

    private function formData(EmailTemplate $template): array
    {
        return ['template' => $template, 'placeholders' => EmailTemplateRenderer::PLACEHOLDERS,
            'sampleData' => $this->renderer->sampleData()];
    }

    private function previewData(Request $request): array
    {
        $samples = $this->renderer->sampleData();
        $submitted = $request->input('sample_data', []);
        if (is_array($submitted)) {
            foreach (array_keys($samples) as $key) {
                if (array_key_exists($key, $submitted) && is_scalar($submitted[$key])) {
                    $samples[$key] = (string) $submitted[$key];
                }
            }
        }
        return $samples;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'email-template';
        $slug = $base;
        $i = 2;
        while (EmailTemplate::where('slug', $slug)->exists()) $slug = $base.'-'.$i++;
        return $slug;
    }
}

<div class="row g-4">

    <div class="col-xl-8">

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h2 class="h6 mb-0">Template details</h2>

            </div>



            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-8">

                        <label class="form-label" for="name">

                            Template name

                            <span class="text-danger">*</span>

                        </label>



                        <input

                            id="name"

                            name="name"

                            class="form-control"

                            maxlength="150"

                            required

                            value="{{ old('name', $template->name) }}"

                            placeholder="e.g. Invoice reminder"

                        >

                    </div>



                    <div class="col-md-4">

                        <label class="form-label" for="category">

                            Category

                        </label>



                        <select

                            id="category"

                            name="category"

                            class="form-select"

                            required

                        >

                            @foreach ([

                                'general' => 'General',

                                'customer' => 'Customer',

                                'invoice' => 'Invoice',

                                'job' => 'Job / Work Order',

                                'lead' => 'Lead',

                                'campaign' => 'Campaign',

                            ] as $key => $label)

                                <option

                                    value="{{ $key }}"

                                    @selected(old('category', $template->category) === $key)

                                >

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>



                    <div class="col-12">

                        <label class="form-label" for="description">

                            Internal description

                        </label>



                        <textarea

                            id="description"

                            name="description"

                            rows="2"

                            class="form-control"

                            maxlength="2000"

                            placeholder="When should this template be used?"

                        >{{ old('description', $template->description) }}</textarea>

                    </div>



                    <div class="col-12">

                        <label class="form-label" for="subject">

                            Email subject

                            <span class="text-danger">*</span>

                        </label>



                        <input

                            id="subject"

                            name="subject"

                            class="form-control"

                            maxlength="255"

                            required

                            value="{{ old('subject', $template->subject) }}"

                            placeholder="Invoice @{{invoice_number}} from @{{company_name}}"

                        >



                        <div class="form-text">

                            You can use the same placeholders in the subject

                            and body.

                        </div>

                    </div>



                    <div class="col-12">

                        <div

                            class="d-flex align-items-center justify-content-between gap-2"

                        >

                            <label class="form-label mb-2" for="body_html">

                                Email body (HTML)

                                <span class="text-danger">*</span>

                            </label>



                            <button

                                type="button"

                                id="toggle-source"

                                class="btn btn-sm btn-outline-secondary mb-2"

                            >

                                Edit HTML source

                            </button>

                        </div>



@php
                            $defaultBody = <<<'HTML'
<h2>Hello {{customer_name}},</h2>
<p>Thank you for working with {{company_name}}.</p>
<p>Regards,<br>{{sender_name}}</p>
HTML;

                            $bodyHtml = old(
                                'body_html',
                                $template->body_html ?: $defaultBody
                            );
                        @endphp

                        <textarea

                            id="body_html"

                            name="body_html"

                            class="form-control"

                            rows="16"

                            required

                        >{{ $bodyHtml }}</textarea>



                        <div class="form-text">

                            Use inline styles for best compatibility across

                            email clients. Avoid JavaScript and external

                            form elements.

                        </div>

                    </div>



                    <div class="col-12">

                        <div class="form-check form-switch">

                            <input

                                class="form-check-input"

                                type="checkbox"

                                role="switch"

                                id="status"

                                name="status"

                                value="1"

                                @checked(

                                    old(

'status',

                                        $template->exists

                                            ? $template->status

                                            : true

                                    )

                                )

                            >



                            <label

                                class="form-check-label"

                                for="status"

                            >

                                Active — available for future CRM workflows

                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="col-xl-4">

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h2 class="h6 mb-0">Available placeholders</h2>

            </div>



            <div class="card-body">

                <p class="text-muted small">

                    Click a placeholder to copy it. Your CRM workflow can

                    replace these values with real record data later.

                </p>



                <div class="d-flex flex-wrap gap-2">

                    @foreach ($placeholders as $key => $label)

                        @php
                            $placeholder = '{{' . $key . '}}';
                        @endphp

                        <button
                            type="button"
                            class="btn btn-sm btn-light border placeholder-copy"
                            data-placeholder="{{ $placeholder }}"
                            title="{{ $label }}"
                        >
                            <code>{{ $placeholder }}</code>
                        </button>

                    @endforeach

                </div>



                <div

                    class="small text-muted mt-3"

                    id="copy-status"

                    aria-live="polite"

                ></div>

            </div>

        </div>



        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <h2 class="h6 mb-0">Sample data</h2>

            </div>



            <div class="card-body">

                <p class="text-muted small">

                    Preview uses sample values. Test emails also use

                    sample data.

                </p>



                <div class="d-grid">

                    <a

                        href="{{ $template->exists

                            ? route('crm.email-templates.preview', $template)

                            : '#' }}"

                        class="btn btn-outline-secondary"

                        @if (!$template->exists)

                            onclick="alert('Save this template before previewing it.'); return false;"

                        @endif

                    >

                        <i class="bi bi-eye me-1"></i>

                        Preview rendered email

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>



@push('styles')

    <link

        href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css"

        rel="stylesheet"

    >



    <style>

        .note-editor.note-frame {

            border-color: #dee2e6;

            border-radius: 0.5rem;

        }



        .note-editor .note-editing-area .note-editable {

            min-height: 300px;

        }



        .placeholder-copy {

            max-width: 100%;

            overflow-wrap: anywhere;

        }

    </style>

@endpush



@push('scripts')

    {{-- Remove this script if jQuery is already loaded in layouts.admin --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>



    <script>

        (function () {

            const textarea = document.getElementById('body_html');

            const toggleButton = document.getElementById('toggle-source');

            const copyStatus = document.getElementById('copy-status');

            const form = textarea.closest('form');



            let sourceMode = false;



            $('#body_html').summernote({

                height: 320,

                dialogsInBody: true,

                toolbar: [

                    ['style', ['style']],

                    ['font', ['bold', 'italic', 'underline', 'clear']],

                    ['para', ['ul', 'ol', 'paragraph']],

                    ['insert', ['link', 'hr']],

                    ['view', ['codeview']]

                ],

                callbacks: {

                    onChange: function (contents) {

                        textarea.value = contents;

                    },



                    onInit: function () {

                        textarea.value = $('#body_html').summernote('code');

                    }

                }

            });



            toggleButton.addEventListener('click', function () {

                sourceMode = !sourceMode;



                $('#body_html').summernote(

                    sourceMode

                        ? 'codeview.activate'

                        : 'codeview.deactivate'

                );



                this.textContent = sourceMode

                    ? 'Return to visual editor'

                    : 'Edit HTML source';

            });



            document.querySelectorAll('.placeholder-copy').forEach(

                function (button) {

                    button.addEventListener('click', async function () {

                        const value = this.dataset.placeholder;



                        try {

                            await navigator.clipboard.writeText(value);



                            copyStatus.textContent = value + ' copied.';

                        } catch (error) {

                            copyStatus.textContent =

                                'Copy unavailable. Select and copy ' + value;

                        }

                    });

                }

            );



            form.addEventListener('submit', function () {

                const editor = $('#body_html');

                const codeView = editor

                    .next('.note-editor')

                    .find('.note-codable');



                if (codeView.is(':visible')) {

                    editor.val(codeView.val());

                } else {

                    editor.val(editor.summernote('code'));

                }

            });

        })();

    </script>

@endpush

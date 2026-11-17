<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $renderedSubject,
        public string $renderedHtml,
    ) {}

    public function build(): static
    {
        return $this->subject($this->renderedSubject)
            ->html($this->renderedHtml);
    }
}

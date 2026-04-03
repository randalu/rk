<?php

namespace App\Mail;

use App\Models\Bill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DueReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Bill $bill,
        public string $subjectLine,
        public string $bodyText
    ) {
    }

    public function build(): static
    {
        $pdf = Pdf::loadView('bills.print', [
            'bill' => $this->bill,
            'isPdf' => true,
        ])->setPaper('a5', 'portrait');

        return $this->subject($this->subjectLine)
            ->view('emails.due-reminder')
            ->with([
                'bill' => $this->bill,
                'bodyText' => $this->bodyText,
                'companyName' => systemSetting('company_name', config('app.name')),
            ])
            ->attachData(
                $pdf->output(),
                'Invoice-' . str_pad((string) $this->bill->id, 5, '0', STR_PAD_LEFT) . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}

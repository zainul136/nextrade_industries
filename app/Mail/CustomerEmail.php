<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    use SerializesModels;

    public $emailContent;
    public $imageUrl;

    public function __construct($emailContent, $imageUrl)
    {
        $this->emailContent = $emailContent;
        $this->imageUrl = $imageUrl;
    }


    public function build()
    {
        return $this->subject('Your Subject Here')
            ->view('emails.customer_email')
            ->with([
                'emailContent' => $this->emailContent,
                'imageUrl'  =>$this->imageUrl,
            ]);
    }


}

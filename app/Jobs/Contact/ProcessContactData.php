<?php

namespace App\Jobs\Contact;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContactData implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;

    protected $contact;
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function handle()
    {
        return $this->contact;
    }
}

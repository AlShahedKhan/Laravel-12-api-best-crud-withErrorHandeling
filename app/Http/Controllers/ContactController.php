<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Helpers\AuthHelper;
use App\Traits\ApiResponse;
use App\Jobs\Contact\SendContactEmail;
use App\Jobs\Contact\ProcessContactData;
use App\Http\Requests\ContactFormRequest;

class ContactController extends Controller
{
    use ApiResponse;
    public function index()
    {
        AuthHelper::checkAdmin();
        $contacts = Contact::paginate(500);
        ProcessContactData::dispatch($contacts);
        return $this->successResponse('Contacts fetched successfully!', ['data' => $contacts]);
    }
    public function store(ContactFormRequest $request)
    {
        $contact = Contact::create($request->validated());
        SendContactEmail::dispatch($contact);
        return response()->json(['message' => 'Contact form submitted successfully!', 'data' => $contact]);
    }

    public function show(Contact $contact)
    {
        AuthHelper::checkAdmin();
        $contact->is_read = 1;
        $contact->save();
        return $this->successResponse('Contact details fetched successfully!', ['data' => $contact]);
    }
}

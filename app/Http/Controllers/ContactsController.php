<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsValidationRequest;
use App\Jobs\SendEmailContactUsUser;
use App\Models\Contacts;
use Exception;

class ContactsController extends Controller
{
    public function addContactUs(ContactUsValidationRequest $request)
    {
        try {
            $contact = Contacts::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
            if ($contact) {
                SendEmailContactUsUser::dispatch($contact);
                return response()->json([
                    'succsess' => true,
                    'status' => 200,
                    'message' => 'contacts add successfully',
                    'contacts' => $contact
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 200,
                    'mesasge' => 'contacts are not added'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}

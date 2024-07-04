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
                    'success' => true,
                    'status' => 201,
                    'message' => 'Contacts Add Successfully',
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'mesasge' => 'Contacts is Not Added'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}

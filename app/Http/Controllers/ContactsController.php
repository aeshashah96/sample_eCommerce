<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsValidationRequest;
use App\Jobs\SendEmailContactUsUser;
use App\Models\Contacts;

class ContactsController extends Controller
{
    public function addContactUs(ContactUsValidationRequest $request){
        $contact = Contacts::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);

        // dd($contact);
        if($contact){
            SendEmailContactUsUser::dispatch($contact);
            return response()->json([
                'succsess'=>true,
                'message'=>'contacts add successfully',
                'contacts'=>$contact
            ],200);
        }
        else{
            return response()->json([
                'success'=>false,
                'mesasge'=>'contacts are not added'
            ],400);
        }
    }
}

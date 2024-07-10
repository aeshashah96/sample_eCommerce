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
                    'message' => 'Message Send Successfully',
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Message Not Send'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllContactUs(){
        $contact=Contacts::paginate(10,['name','email']);

        if($contact){
            return response()->json(['success'=>true,'status'=>200,'message'=>'ContactUs Data Get Successfully','data'=>$contact]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'Data Not Found']);
        }
    }

    public function showDetailsOfContactUs($id){
        $contactus = Contacts::find($id);
        if($contactus){
            return response()->json(['success'=>true,'status'=>200,'message'=>'ContactUs Data Get Successfully','data'=>$contactus]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'Data Not Found']);
        }
    }
}

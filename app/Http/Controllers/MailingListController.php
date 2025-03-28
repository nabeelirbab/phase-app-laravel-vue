<?php

namespace App\Http\Controllers;

use App\Events\Register;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Rules\EmailListSubscriptionRule;

class MailingListController extends Controller
{
    public function store(Request $request)
    {
        try {
            $emailList = EmailList::firstOrCreate(['name' => 'Newsletter']);

            $data = $this->validate($request, [
                'email' => ['email', new EmailListSubscriptionRule($emailList)]
            ]);

            $emailList->subscribeskippingConfirmation($data['email']);

            return [
                'success' => true,
                'message' => 'You have been subscribed to the mailing list.'
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => 'There was an error subscribing you to the mailing list.'
            ];
        }
    }

    public function auth(Request $request)
    {
        $user = User::where('id', $request->user['id'])->get();
        if (date('Y-d-m', strtotime($user[0]['created_at'])) >= date('Y-d-m')) {
            event(new Register($user[0]));
        }
    }
}

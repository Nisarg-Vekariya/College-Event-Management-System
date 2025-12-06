<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Convert enrolment_no to uppercase before validation
        $request->merge([
            'enrolment_no' => strtoupper($request->enrolment_no)
        ]);

        $request->validate([
            'enrolment_no' => 'required|exists:students,enroll_no',
        ]);

        $student = Student::where('enroll_no', $request->enrolment_no)->first();

        // Prevent duplicate registration
        if (User::where('email', $student->email)->exists()) {
            return back()->with('error', 'You are already registered. Please login.');
        }

        // Generate random password
        $password = Str::random(8);

        // Create user (Laravel users table)
        $user = User::create([
            'enrolment_no' => $student->enroll_no,
            'name' => $student->full_name,
            'email' => $student->email,
            'password' => Hash::make($password),
        ]);

        // Send password email
        Mail::send('emails.account-created', [
    'studentName' => $student->full_name,
    'studentEmail' => $student->email,
    'studentPassword' => $password,
    'loginUrl' => route('login')
], function ($message) use ($student) {
    $message->to($student->email)
        ->subject('Your 818 Uni Event Account Details')
        ->from(config('mail.from.address'), config('mail.from.name'));
});

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully. Please check your email.'
        ]);
    }
}

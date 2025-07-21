<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RegisterController extends Controller
{
    
    public function showRegistrationForm(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'employee_code', 'name', 'email', 'department','role']);
            return DataTables::of($users)->make(true);
        }

        return view('auth.register');
    }
    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }


    public function register(Request $request)
    {
        // dd($request);die;
        // Validate request data
        $this->validator($request->all())->validate();
// dd($a);die;
        // Create user
        $user = $this->create($request->all());
// dd($user);die;
        // Log in user automatically (optional)
        auth()->login($user);

        // Redirect to intended page or dashboard
        return redirect()->route('home')->with('success', 'Employee registered successfully!');
    }

    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'employee_code'      => ['required', 'string', 'max:255', 'unique:users,employee_code'],
            'mobile_no'          => ['nullable', 'string', 'max:15'],
            'gender'             => ['nullable', 'in:Male,Female'],
            'marital_status'     => ['nullable', 'in:Single,Married'],
            'dob'                => ['nullable', 'date'],
            'join_date'          => ['nullable', 'date'],
            'confirmation_date'  => ['nullable', 'date'],
            'probation_months'   => ['nullable', 'integer', 'min:0'],
            'aadhaar'            => ['nullable', 'string', 'max:20'],
            'face_id'            => ['nullable', 'string', 'max:255'],
            'resignation_date'   => ['nullable', 'date'],
            'resignation_reason' => ['nullable', 'string', 'max:255'],
            //  'category'           => ['nullable', 'string', 'max:255'],
            // 'holiday_group'      => ['nullable', 'string', 'max:255'],
            'hours_day'          => ['nullable', 'numeric', 'min:0'],
            'days_week'          => ['nullable', 'integer', 'min:0'],
            'role' => ['required'],

        ]);
    }

    protected function create(array $data)
    {
        // echo"<pre>";
        // 
        return User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'password'             => Hash::make($data['password']),
            'employee_code'        => $data['employee_code'] ?? null,
            'mobile_no'            => $data['mobile_no'] ?? null,
            'gender'               => $data['gender'] ?? null,
            'marital_status'       => $data['marital_status'] ?? null,
            'dob'                  => $data['dob'] ?? null,
            'join_date'            => $data['join_date'] ?? null,
            'confirmation_date'    => $data['confirmation_date'] ?? null,
            'probation_months'     => $data['probation_months'] ?? null,
            'aadhaar'              => $data['aadhaar'] ?? null,
            'face_id'              => $data['face_id'] ?? null,
            'resignation_date'     => $data['resignation_date'] ?? null,
            'resignation_reason'   => $data['resignation_reason'] ?? null,
            'department'           => $data['department'] ?? null,
            // 'section'              => $data['section'] ?? null,
            // 'designation'          => $data['designation'] ?? null,
            // 'category'             => $data['category'] ?? null,
            // 'holiday_group'        => $data['holiday_group'] ?? null,
            'hours_day'            => $data['hours_day'] ?? null,
            'days_week'            => $data['days_week'] ?? null,
            // 'hours_year'           => $data['hours_year'] ?? null,
            // 'employee_type'        => $data['employee_type'] ?? null,
            // 'extra_classification' => $data['extra_classification'] ?? null,
            // 'currency'             => $data['currency'] ?? null,
            // 'manager'              => $data['manager'] ?? null,
            'role' => $data['role'],

        ]);
    }

   
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RegisterController extends Controller
{
    
  
    public function showRegistrationForm(Request $request)
    {
        if ($request->ajax()) {
            $users = DB::table('users')
                ->leftJoin('role', 'users.role', '=', 'role.id')
                ->select([
                    'users.*',
                    'users.id',
                    'users.employee_code',
                    'users.name',
                    'users.email',
                    'users.department',
                    'users.is_active',
                    'role.role as role_name'
                ]);

        return DataTables::of($users)
            ->addColumn('role_name', function($user) {
                return $user->role->role ?? 'N/A';
            })
            ->editColumn('is_active', function($user) {
                return $user->is_active; // must be a field in your table
            })
            ->make(true);

        }

        $roles = DB::table('role')->select('id', 'role')->get();
        return view('auth.register', compact('roles'));
    }

    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }


    public function register(Request $request)
    {
       
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        auth()->login($user);

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
            'hours_day'          => ['nullable', 'numeric', 'min:0'],
            'days_week'          => ['nullable', 'integer', 'min:0'],
            'role' => ['required'],

        ]);
    }

    protected function create(array $data)
    {
       
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
            'hours_day'            => $data['hours_day'] ?? null,
            'salary'               => $data['salary'] ?? null, 
            'days_week'            => $data['days_week'] ?? null,
            'role' => $data['role'],

        ]);
    }

   public function updateStatus(Request $request, $id)
{
    $employee = User::findOrFail($id); 
    $employee->is_active = $request->is_active;
    $employee->save();

    return response()->json(['success' => true]);
}


}

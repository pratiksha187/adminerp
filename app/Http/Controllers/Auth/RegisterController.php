<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables; // ← use the facade

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        // ✅ Get a scalar role id for the logged-in user (null if none)
        $roleId = DB::table('users')->where('id', Auth::id())->value('role');

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
                    'users.role as role_id',
                    DB::raw('COALESCE(role.role, "N/A") as role_name'),
                ]);

            return DataTables::of($users)
                // ✅ already selected role_name; just return it
                ->editColumn('role_name', fn ($u) => $u->role_name)
                ->editColumn('is_active', fn ($u) => (int) $u->is_active)
                ->make(true);
        }

        $roles = DB::table('role')->select('id', 'role')->get();

        // ✅ pass $roleId to the view
        return view('auth.register', compact('roles', 'roleId'));
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
        // auth()->login($user);
        return redirect()->route('register')->with('success', 'Employee registered successfully!');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            // 'employee_code'     => ['required', 'string', 'max:255', 'unique:users,employee_code'],
            'mobile_no'         => ['nullable', 'string', 'max:15'],
            'gender'            => ['nullable', 'in:Male,Female'],
            'marital_status'    => ['nullable', 'in:Single,Married'],
            'dob'               => ['nullable', 'date'],
            'join_date'         => ['nullable', 'date'],
            'confirmation_date' => ['nullable', 'date'],
            'probation_months'  => ['nullable', 'integer', 'min:0'],
            'aadhaar'           => ['nullable', 'string', 'max:20'],
            'hours_day'         => ['nullable', 'numeric', 'min:0'],
            'days_week'         => ['nullable', 'integer', 'min:0'],
            'role'              => ['required'],
            'insurance'         => ['required'],
            'pt'                => ['required'],
            'advance'           => ['required'],
            'pf'                => ['required']
        ]);
    }

    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name'              => $data['name'],
    //         'email'             => $data['email'],
    //         'password'          => Hash::make($data['password']),
    //         'employee_code'     => $data['employee_code'] ?? null,
    //         'mobile_no'         => $data['mobile_no'] ?? null,
    //         'gender'            => $data['gender'] ?? null,
    //         'marital_status'    => $data['marital_status'] ?? null,
    //         'dob'               => $data['dob'] ?? null,
    //         'join_date'         => $data['join_date'] ?? null,
    //         'confirmation_date' => $data['confirmation_date'] ?? null,
    //         'probation_months'  => $data['probation_months'] ?? null,
    //         'aadhaar'           => $data['aadhaar'] ?? null,
    //         'hours_day'         => $data['hours_day'] ?? null,
    //         'salary'            => $data['salary'] ?? null,
    //         'days_week'         => $data['days_week'] ?? null,
    //         'role'              => $data['role'],
    //         'insurance'         => $data['insurance'],
    //         'pt'                => $data['pt'],
    //         'advance'           => $data['advance'],
    //         'pf'                => $data['pf'],
    //         'cl'                => 4,
    //         'sl'                => 4,
    //         'el'                => 4 
    //     ]);
    // }
protected function create(array $data)
{
    // Get the last user with an employee_code
    $lastUser = User::whereNotNull('employee_code')
        ->orderBy('id', 'desc')
        ->first();

    // Generate next employee code
    if ($lastUser && preg_match('/^SC(\d+)$/', $lastUser->employee_code, $matches)) {
        $nextNumber = (int)$matches[1] + 1;
    } else {
        $nextNumber = 1;
    }

    // Format code like SC001
    $employeeCode = 'SC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    return User::create([
        'name'              => $data['name'],
        'email'             => $data['email'],
        'password'          => Hash::make($data['password']),
        'employee_code'     => $employeeCode, // Auto-generated
        'mobile_no'         => $data['mobile_no'] ?? null,
        'gender'            => $data['gender'] ?? null,
        'marital_status'    => $data['marital_status'] ?? null,
        'dob'               => $data['dob'] ?? null,
        'join_date'         => $data['join_date'] ?? null,
        'confirmation_date' => $data['confirmation_date'] ?? null,
        'probation_months'  => $data['probation_months'] ?? null,
        'aadhaar'           => $data['aadhaar'] ?? null,
        'hours_day'         => $data['hours_day'] ?? null,
        'salary'            => $data['salary'] ?? null,
        'days_week'         => $data['days_week'] ?? null,
        'role'              => $data['role'] ?? null,
        'insurance'         => $data['insurance'] ?? null,
        'pt'                => $data['pt'] ?? null,
        'advance'           => $data['advance'] ?? null,
        'pf'                => $data['pf'] ?? null,
        'cl'                => 4,
        'sl'                => 4,
        'el'                => 4,
    ]);
}

    public function updateStatus(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $employee->is_active = $request->is_active;
        $employee->save();

        return response()->json(['success' => true]);
    }

   public function show(User $user)
{
    $roleRow = DB::table('role')
        ->select('id','role')
        ->where('id', $user->role)
        ->first();

    return response()->json([
        'id'                  => $user->id,
        'employee_code'       => $user->employee_code,
        'name'                => $user->name,
        'email'               => $user->email,
        'mobile_no'           => $user->mobile_no,
        'role_id'             => $user->role,                 // ✅ use users.role
        'role_name'           => $roleRow?->role,             // optional, handy in UI
        'salary'              => $user->salary,
        'gender'              => $user->gender,
        'marital_status'      => $user->marital_status,
        'aadhaar'             => $user->aadhaar,
        'dob'                 => $user->dob,
        'join_date'           => $user->join_date,
        'confirmation_date'   => $user->confirmation_date,
        'probation_months'    => $user->probation_months,
        'hours_day'           => $user->hours_day,
        'days_week'           => $user->days_week,
        'insurance' => $user->insurance,
        'advance' => $user->advance,
        'pf' => $user->pf,
        'pt' => $user->pt,

        'is_active'           => $user->is_active,
    ]);
}


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'employee_code'     => ['required', 'string', Rule::unique('users', 'employee_code')->ignore($user->id)],
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile_no'         => ['nullable', 'string', 'max:20'],
            'role'              => ['nullable', 'integer'], // role_id
            'salary'            => ['nullable', 'numeric'],
            'gender'            => ['nullable', Rule::in(['Male','Female'])],
            'marital_status'    => ['nullable', Rule::in(['Single','Married'])],
            'aadhaar'           => ['nullable', 'string', 'max:20'],
            'dob'               => ['nullable', 'date'],
            'join_date'         => ['nullable', 'date'],
            'confirmation_date' => ['nullable', 'date'],
            'probation_months'  => ['nullable', 'integer'],
            'hours_day'         => ['nullable', 'numeric'],

            'days_week'         => ['nullable', 'integer'],
            'is_active'         => ['required', Rule::in([0,1,'0','1'])],
            'insurance'         => ['nullable'],
            'pt'                => ['nullable'],
            'advance'           => ['nullable'],
            'pf'                => ['nullable'],
            'password'          => ['nullable', 'confirmed', 'min:6'],
        ]);

        $user->employee_code     = $validated['employee_code'];
        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->mobile_no         = $validated['mobile_no'] ?? null;
        $user->salary            = $validated['salary'] ?? null;
        $user->gender            = $validated['gender'] ?? null;
        $user->marital_status    = $validated['marital_status'] ?? null;
        $user->aadhaar           = $validated['aadhaar'] ?? null;
        $user->dob               = $validated['dob'] ?? null;
        $user->join_date         = $validated['join_date'] ?? null;
        $user->confirmation_date = $validated['confirmation_date'] ?? null;
        $user->probation_months  = $validated['probation_months'] ?? null;
        $user->hours_day         = $validated['hours_day'] ?? null;
        $user->days_week         = $validated['days_week'] ?? null;
        $user->insurance = $validated['insurance'] ?? null;
        $user->pt  = $validated['pt'] ?? null;
        $user->advance         = $validated['advance'] ?? null;
        $user->pf         = $validated['pf'] ?? null;
        $user->is_active         = (int)($validated['is_active'] ?? 1);

        // If you store role_id on users table
      
        if (array_key_exists('role', $validated) && !is_null($validated['role'])) {
            $user->role = $validated['role'];   // ✅ not $user->role_id
        }

        // If password provided, update it
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // If you're using Spatie roles instead of role_id:
        // if (!empty($validated['role'])) {
        //     $user->syncRoles([$validated['role']]); // adjust to role name if needed
        // }

        return response()->json(['status' => 'ok']);
    }

}

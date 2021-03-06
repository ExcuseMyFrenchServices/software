<?php
namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\EditCredentialsRequest;
use App\Http\Requests\User\PasswordUpdateRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Profile;
use App\Role;
use App\User;
use App\Assignment;
use App\Event;
use App\Services\UsersMissions;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['passwordEdit']
        ]);

        $this->middleware('admin', [
            'except' => ['passwordEdit', 'passwordEditForm', 'passwordUpdate', 'edit','update','payroll']
        ]);
    }

    public function index()
    {
        $users = User::all();

        return view('user.index')->with(compact('users'));
    }

    public function search(Request $search)
    {
        $users = User::join('profiles', 'profiles.user_id', '=', 'users.id')
                ->where('last_name','=',$search->search)
                ->orWhere('first_name','=',$search->search)
                ->orWhere('email','=',$search->search)
                ->get();
        return view('user.search', compact('users', 'search'));
    }

    public function create()
    {

        $roles = Role::all();

        return view('user.create')->with(compact('roles'));
    }

    public function store(CreateUserRequest $request)
    {
        // Generate username
        $username = strtolower(substr($request->input('first_name'), 0, 1) . $request->input('last_name'));

        // Prevent duplicate username
        $count = User::where('username', 'like', $username . '%')->count();
        $username = ($count > 0) ? $username . strval($count + 1) : $username;

        // Create new user
        $user = User::create([
            'username'  => $username,
            'password'  => Hash::make('emf2015'),
            'role_id'   => $request->input('role'),
            'hash'      => str_random(15),
        ]);

        // Setup profile
        Profile::create([
            'email'             => $request->input('email'),
            'first_name'        => $request->input('first_name'),
            'last_name'         => $request->input('last_name'),
            'phone_number'      => $request->input('phone_number'),
            'rsa_number'        => $request->input('rsa_number'),
            'drivers_license'   => $request->input('drivers_license'),
            'has_car'           => $request->input('has_car') == 'on',
            'address'           => $request->input('address'),
            'shirt_size'        => $request->input('shirt_size'),
            'pant_size'         => $request->input('pant_size'),
            'shoe_size'         => $request->input('shoe_size'),
            'user_id'           => $user->id,
        ]);

        // Send notification
        Mail::send('emails.welcome', ['user' => $user, 'hash' => $user->hash], function($message) use ($request) {
            $message->to($request->input('email'))->subject('Welcome to the team!');
        });

        return redirect('user');
    }

    public function edit($userId)
    {
        
        if (Auth::user()->role_id != 1 && Auth::user()->id != $userId) {
            return redirect('/events/'.$userId);
        }
        
        $roles = Role::all();

        $user = User::find($userId);

        $profile = $user->profile;
        
        $userMissions = new UsersMissions;
        $userClientMission = $userMissions->getUserMissions($userId);

        $userTotalMissions = $userMissions->getUserTotalMissions($userId);

        $userBestClient = $userMissions->getUserBestClient($userId);

        return view('user.create')->with(compact('roles', 'user', 'profile', 'userClientMission', 'userTotalMissions', 'userBestClient'));
    }

    public function update(UpdateUserRequest $request, $userId)
    {

        if (Auth::user()->role_id != 1 && Auth::user()->id != $userId) {
            return redirect('/events/'.$userId);
        }
        
        $user = User::find($userId);
        $profile = $user->profile;

        if(Auth::user()->role_id == 1)
        {    
            $user->role_id = $request->input('role');
            $user->level = $request->input('level');
            $user->save();
        }

        $profile->email             = $request->input('email');
        $profile->phone_number      = $request->input('phone_number');
        $profile->rsa_number        = $request->input('rsa_number');
        $profile->drivers_license   = $request->input('drivers_license');
        $profile->has_car           = $request->input('has_car') == 'on';
        $profile->address           = $request->input('address');
        $profile->shirt_size        = $request->input('shirt_size');
        $profile->pant_size         = $request->input('pant_size');
        $profile->shoe_size         = $request->input('shoe_size');
        $profile->save();

        Session::flash('success', 'User successfully updated');

        return redirect()->back();
    }

    public function destroy($userId)
    {
        if ($userId != 1) {
            $user = User::find($userId);
            $user->delete();
        }

        return redirect('user');
    }


    public function passwordEdit($hash)
    {
        if (!Auth::check()) {
            $user = User::where('hash', $hash)->get()->first();

            $user->hash = '';
            $user->save();

            Auth::loginUsingId($user->id);
        } else {
            $user = Auth::user();
        }

        return view('user.edit-password')->with('id' , $user->id);
    }

    public function passwordEditForm($userId)
    {
        if (Auth::user()->id != $userId) {
            return redirect('/');
        }

        return view('user.edit-password')->with('id' , $userId);
    }

    public function passwordUpdate(PasswordUpdateRequest $request, $userId)
    {
        $user = User::find($userId);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect('events/' . $user->id);
    }

    public function editCredentialsForm($userId)
    {
        $user = User::find($userId);

        return view('user.edit-credentials')->with(compact('user'));
    }

    public function editCredentials(EditCredentialsRequest $request, $userId)
    {
        $user = User::find($userId);

        $user->username = $request->get('username');

        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        $user->save();

        Session::flash('success', 'User successfully updated');

        return redirect()->back();
    }

    public function archiveIndex()
    {
        $users = User::where('role_id',12)->get();

        return view('user.archived')->with(compact('users'));
    }

    public function archive($user)
    {
        $user = User::find($user);

        $user->role_id = 12;
        $user->save();

        return redirect()->back();
    }

    public function unarchive($user)
    {
        $user = User::find($user);

        $user->role_id = 2;
        $user->save();

        return redirect()->back();
    }

    public function payroll($userId,$week=null){
        if(Auth::user()->role_id != 1 && Auth::user()->id != $userId){
            return redirect('/');
        }

        $user = User::find($userId);
        if($user !== null){
            $userService = new UserService($user);
            $payrolls = $userService->getPayrolls();
            if($week == null){
                $week = date('W');
            }
            
            $year = date('Y');
            $nextWeek = $week+1;
            $lastWeek = $week-1;

            return view('reports.payroll')->with(compact('user','payrolls','year','week','nextWeek','lastWeek','userService'));
        } else {
            return redirect('/');
        }
    }
}

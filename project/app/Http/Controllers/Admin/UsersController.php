<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('user_access')) {
            return abort(401);
        }
        if (request('show_deleted') == 1) {
            if (! Gate::allows('user_delete')) {
                return abort(401);
            }
            $users = User::onlyTrashed()->get();
        } else {
            $users = User::get();
        }
        
        //$users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $roles = \App\Role::get()->pluck('title', 'id');
        $areas = \App\Models\Area::get()->pluck('title', 'id');
        $employees = DB::table('employees')->where('status', '=', 'active')->pluck('name','id');
        $emps = array();
        foreach($employees as $key=>$emp)
        {
            $emps[]=array('value'=>$key,'label'=>$emp);
        }
        return view('admin.users.create', compact('roles','areas','emps'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $employees = DB::table('users')->where('employee_id', '=', $request->employee_id)->first();
        if($employees)
            return redirect()->route('admin.users.create')->withErrors('This employee already associated with an user.');
        $user = User::create($request->all());
        $user->area()->sync(array_filter((array)$request->input('area')));
        $user->role()->sync(array_filter((array)$request->input('role')));



        return redirect()->route('admin.users.index')->with('successmsg', 'User added successfully.');
    }


    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $roles = \App\Role::get()->pluck('title', 'id');
        $areas = \App\Models\Area::get()->pluck('title', 'id');
        $user = User::findOrFail($id);
        $employees = \App\Models\Employee::where('status', 'active')->get()->pluck('name', 'id');
        $employees->prepend('Select an employee', '');
        $clients = \App\Models\Client::where('status', 'active')->get()->pluck('name', 'id');
        $clients->prepend('Select a Client', '');

        // dd($clients);
        return view('admin.users.edit', compact('user', 'roles','areas','employees','clients'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        //echo '<pre>';print_r($request->all());die;
        if($request->employee_id)
        {
            $employees = DB::table('users')
                    ->where('employee_id', '=', $request->employee_id)
                    ->where('id', '!=', $id)
                    ->first();
            if($employees)
                return redirect()->route('admin.users.edit',[$id])->withErrors('This Employee is already associated with a user.');
        }
        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->area()->sync(array_filter((array)$request->input('area')));
        $user->role()->sync(array_filter((array)$request->input('role')));



        return redirect()->route('admin.users.index')->with('successmsg', 'User updated successfully.');
    }


    /**
     * Display User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('user_view')) {
            return abort(401);
        }
        $roles = \App\Role::get()->pluck('title', 'id');

        $user = User::with(array('employee'))->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }


    
    /**
     * get list of clients.
     * @return \Illuminate\Http\Response
     */
    public function getClients(Request $request)
    {
        if($request->client) return redirect()->route('admin.users.invite_client', $request->client);
        $clients = \App\Models\Client::where('status', 'active')->get()->pluck('name', 'id');
        return view('admin.users.select_client', 
        [
            'clients'=> $clients
        ]
        );

    }
        
    /**
     * Invite a user.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function inviteClient(Request $request, int $id)
    {
        if (! Gate::allows('user_create')) {
            return abort(401);
        }

        $client = \App\Models\Client::findOrFail($id);
        $users = \App\User::where('client_id',$id)->get();
        $client->email = $client->billing_contact_email;



        $roles = \App\Role::get()->pluck('title', 'id');

        if($request->isMethod('post'))
        {

            if($request->resend_invitation)
            {
                $user = \App\User::findOrFail($request->user_id);
            }
            else{

                $request->validate([
                    'email' => 'required|email|unique:users|max:150',
                ]);
    
                
                $user = \App\User::create([
                        'name' => explode("@",$request->email)[0],
                        'email' => $request->email,
                        'status' => 'inactive',
                        'crystal_token' => Str::uuid(),
                        'client_id' => $client->id
                    ]);
    
                $user->role()->sync($request->role);

            }

            
            

            // sending invitation mail

            Mail::to($user->email)->send(new \App\Mail\UserInvite($user));

            $users = \App\User::where('client_id',$id)->get();
            
            return back()->with('users', $users)->with('successmsg', 'Invitation send successfully.');
        }

        return view('admin.users.invite', 
        [
            'roles' => $roles,
            'client'=> $client,
            'users'=> $users
        ]
        );
        // return redirect()->route('admin.users.index')->with('successmsg', 'User added successfully.');
    }


    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('successmsg', 'User set as inactive successfully.');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
    
    /**
     * Restore User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')->with('successmsg', 'User set as active successfully.');
    }

    /**
     * Permanently delete User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('admin.users.index')->with('successmsg', 'User deleted successfully.');
    }

}

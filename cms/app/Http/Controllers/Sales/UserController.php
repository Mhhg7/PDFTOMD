<?php

namespace App\Http\Controllers\Sales;

use App\Models\SalesActivity;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Users and their roles. Managers handle Viewer to Manager; only Admins touch Admins. */
class UserController extends SalesController
{
    public function index()
    {
        return view('sales.users', ['users' => User::query()->orderByDesc('active')->orderBy('name')->get()]);
    }

    public function create(Request $request)
    {
        return view('sales.user', ['u' => new User(['role' => 'viewer', 'active' => true]), 'roles' => Roles::assignableBy($request->user())]);
    }

    public function store(Request $request)
    {
        $d = $this->validated($request, null);
        $u = User::query()->create($d + ['active' => true]);
        SalesActivity::log('user.created', $u->email, 'Role: '.$u->roleLabel());

        return redirect()->route('sales.users.index')->with('ok', "{$u->name} can now sign in at ".url('/admin/login').'.');
    }

    public function edit(Request $request, User $user)
    {
        $this->guard($request, $user);

        return view('sales.user', ['u' => $user, 'roles' => Roles::assignableBy($request->user())]);
    }

    public function update(Request $request, User $user)
    {
        $this->guard($request, $user);
        $d = $this->validated($request, $user);
        $self = $user->is($request->user());
        if ($self) {
            unset($d['role']); // nobody changes their own role or switches themselves off
        } else {
            $d['active'] = $request->boolean('active');
        }
        if (empty($d['password'])) {
            unset($d['password']);
        }
        $user->fill($d);
        $changed = array_keys($user->getDirty());
        $user->save();
        if ($changed) {
            SalesActivity::log('user.updated', $user->email, 'Changed: '.implode(', ', $changed).' · role '.$user->roleLabel().($user->active ? '' : ' · switched off'));
        }

        return redirect()->route('sales.users.index')->with('ok', "{$user->name} saved.");
    }

    public function destroy(Request $request, User $user)
    {
        $this->guard($request, $user);
        if ($user->is($request->user())) {
            return back()->with('err', 'You cannot delete your own account.');
        }
        $user->delete();
        SalesActivity::log('user.deleted', $user->email);

        return redirect()->route('sales.users.index')->with('ok', "{$user->name} removed.");
    }

    private function guard(Request $request, User $user): void
    {
        abort_if(Roles::rank($user->role) > Roles::rank($request->user()->role), 403, 'Only an Admin can change an Admin.');
    }

    private function validated(Request $request, ?User $user): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:200', Rule::unique('users', 'email')->ignore($user?->id)],
            // Your own role is never taken from the form, so it is not checked either.
            'role' => $user && $user->is($request->user()) ? ['nullable'] : ['required', Rule::in(Roles::assignableBy($request->user()))],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(10)],
        ]);
    }
}

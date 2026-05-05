<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Observers\UserObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    protected $userObserver;
    protected $uploadImageProcess;

    public function __construct(UserObserver $userObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->userObserver         = $userObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }


    public function index()
    {
        return view('profile', ['page'   => __('page.my_profile'), 'breadcumb' => true]);
    }

    public function profile(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|unique:users,email,' . my_user()->id,
            'phone'     => 'required|unique:users,phone,' . my_user()->id,
            'image'     => 'mimes:jpg,jpeg,png',
            'gender'    => 'required|in:male,female'
        ]);

        $image  = '';

        if ($request->image) {
            $this->unlinkFile(my_user()->photo);
            $image =  $this->uploadImage($request, 'image', 'users');
        }

        $this->userObserver->updateData($request, my_user(), $image);
        return redirect()->back()->with(['flash'    => __('general.success_update')]);
    }

    public function password(Request $request)
    {
        $this->validate($request, [
            'old_password'  => 'required',
            'password'      => 'required|min:8',
            'confirm'       => 'required',
        ]);

        if ($request->password != $request->confirm) {
            return back()->with(['gagal' => __('validation.password_must_same')]);
        }

        $passwordCheck  = $this->userObserver->getCheckUserPassword($request, my_user());

        if (!$passwordCheck) {
            return back()->with(['gagal'    => 'Password Lama Anda Salah']);
        }

        $this->userObserver->changePassword($request, my_user());
        return redirect()->back()->with(['flash'    => __('general.success_update')]);
    }

    public function myPermissions()
    {
        try {
            $user = auth()->user();
            $permissions = $this->userObserver->getUserPermissions($user);

            return response()->json([
                'success' => true,
                'message' => 'Data permission berhasil diambil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => [
                            'id' => $user->role_access?->id,
                            'name' => $user->role_access?->name,
                        ],
                    ],
                    'permissions' => $permissions,
                    'permission_names' => $permissions->pluck('name')->toArray(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permission',
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile()
            ], 500);
        }
    }
}

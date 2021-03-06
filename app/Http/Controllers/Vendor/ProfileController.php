<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function view()
    {
        return view('vendor-views.profile.index');
    }
    
    public function bank_view()
    {
        $data = Vendor::where('id', Helpers::get_restaurant_id())->first();
        return view('vendor-views.profile.bankView', compact('data'));
    }

    public function edit()
    {
        $data = Vendor::where('id',Helpers::get_restaurant_id())->first();
        return view('vendor-views.profile.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $table=auth('vendor')->check()?'vendors':'vendor_employees';
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|unique:'.$table.',email,'.Helpers::get_vendor_id(),
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:'.$table.',phone,'.Helpers::get_vendor_id(),
        ], [
            'f_name.required' => trans('messages.first_name_is_required'),
        ]);
        $seller = auth('vendor')->check()?auth('vendor')->user():auth('vendor_employee')->user();
        $seller->f_name = $request->f_name;
        $seller->l_name = $request->l_name;
        $seller->phone = $request->phone;
        $seller->email = $request->email;
        if ($request->image) {
            $seller->image = Helpers::update('vendor/', $seller->image, 'png', $request->file('image'));
        }
        $seller->save();

        Toastr::success(trans('messages.profile_updated_successfully'));
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required',
        ]);

        $seller = Vendor::find(Helpers::get_restaurant_id());
        $seller->password = bcrypt($request['password']);
        $seller->save();
        Toastr::success(trans('messages.vendor_pasword_updated_successfully'));
        return back();
    }

    public function bank_update(Request $request)
    {
        $bank = Vendor::find(Helpers::get_restaurant_id());
        $bank->bank_name = $request->bank_name;
        $bank->branch = $request->branch;
        $bank->holder_name = $request->holder_name;
        $bank->account_no = $request->account_no;
        $bank->save();
        Toastr::success(trans('messages.bank_info_updated_successfully'));
        return redirect()->route('vendor.profile.bankView');
    }

    public function bank_edit()
    {
        $data = Vendor::where('id', Helpers::get_restaurant_id())->first();
        return view('vendor-views.profile.bankEdit', compact('data'));
    }

}

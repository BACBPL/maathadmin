<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDetail;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function createBasic()
    {
        return view('pages.panel.create_employee');
    }

    public function storeBasic(Request $r)
    {
        $data = $r->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'required|string',
            'email'          => 'required|string',
            'phone'          => 'required|string|max:20',
            'personal_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($file = $r->file('personal_image')) {
            $base     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time().'_'.Str::slug($base).'.'.$file->getClientOriginalExtension();
            $dest     = public_path('assets/img/vendors/personal');
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $filename);
            $data['photo'] = "assets/img/employees/personal/{$filename}";
        }
           $data['password'] = Hash::make("12345678");
        $employee = EmployeeDetail::create($data);

        // Redirect to the same blade, now in "Step 2" mode because $vendor exists
        return redirect()
            ->route('panel.employee.docs.create', $employee);
    }

    public function createDocs(EmployeeDetail $employee)
    {
        return view('pages.panel.create_employee', compact('employee'));
    }

    public function storeDocs(Request $r, EmployeeDetail $employee)
    {
        $data = $r->validate([
            'aadhar_number'        => 'required|string',
            'aadhar_image'         => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_number'           => 'required|string',
            'pan_image'            => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
        ]);

        foreach (['aadhar','pan'] as $field) {
            $file = $r->file("{$field}_image");
            $name = Str::slug($data["{$field}_number"]);
            $fn   = time()."_{$field}_{$name}.{$file->extension()}";
            $dest = public_path("assets/img/employees/{$field}");
            File::ensureDirectoryExists($dest, 0755, true);
            $file->move($dest, $fn);
            $data["{$field}_image"] = "assets/img/employees/{$field}/{$fn}";
        }

        EmployeeDocument::create(array_merge(
            ['employee_detail_id' => $employee->id],
            $data
        ));

        return redirect()
            ->route('panel.dashboard')
            ->with('success','Employee created successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\ReportResource;

class DepartmentReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Department $department)
    {
        // $reports = $department->reports;
        // return ReportResource::collection($reports);
        return $department->reports()->paginate(1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}

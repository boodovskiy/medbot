<?php

namespace App\Http\Controllers\Admin;

use App\Gender;
use App\Http\Requests\Admin\PatientAddRequest;
use App\Http\Requests\Admin\PatientUpdateRequest;
use App\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $patients = Patient::paginate(10);
        } elseif (Auth::user()->isDoc()) {
            $patients = Patient::where('user_id', Auth::user()->id)->orderBy('surname', 'asc')->paginate(10);
        }
        return view('admin.patient.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $genders = Gender::pluck('name', 'id')->all();
        return view('admin.patient.create', compact('genders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PatientAddRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $patient = Patient::create($input);
        Session::flash('add', 'Пациент ' . $patient->name . ' ' . $patient->surname . ' добавлен');
        return redirect()->route('patient.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        if ($patient->user_id == Auth::user()->id || Auth::user()->isAdmin()) {
            $genders = Gender::pluck('name', 'id')->all();
            return view('admin.patient.edit', compact('patient', 'genders'));
        } else {
            return redirect()->route('patient.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PatientUpdateRequest $request, $id)
    {
        $input = $request->all();
        $patient = Patient::findOrFail($id);
        $patient->update($input);
        Session::flash('edit', 'Пациент ' . $patient->name . ' ' . $patient->surname . ' обновлен');
        return redirect()->route('patient.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        Session::flash('del', 'Пациент ' . $patient->name . ' ' . $patient->surname . ' удален');
        return redirect()->route('patient.index');
    }
}

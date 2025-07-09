<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassroomRequest;
use App\Models\Classroom;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class ClassroomsController extends Controller
{
    // originally passed $request variables in all methods.
    /**
     * Display a listing of the resource.
     */
    // I mean it's was public function index(Request $request)
    public function index()
    {
        // return collection of Classrooms.
        $classrooms = Classroom::orderBy('created_at', 'DESC')->get();
        $success = session('success');
//        Session::remove('success');

        return view('classrooms.index', compact('classrooms', 'success'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Method 1
        return view('classrooms.create');;
        // Method 2
//        return view()->make('classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassroomRequest $request)
    {
        // Method 1
//        $classroom = new Classroom();
//        $classroom->name = $request->post('name');
//        $classroom->section = $request->post('section');
//        $classroom->subject = $request->post('subject');
//        $classroom->room = $request->post('room');
//        $classroom->code = Str::random(8);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $path = $file->storeAs('public/cover_images', $file->hashName());
//            $path = $file->store('public/cover_images', 'public');

//            $request->merge([
//                'cover_image' => $path,
//            ]);
            $validated['cover_image'] = $path;
        }

        DB::beginTransaction();
        try{
            $classroom = Classroom::create($request->all());
            $classroom->join(Auth::id(), 'teacher');

            DB::commit();
        }catch(QueryException $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }

        // Method 2
//        $request->merge([
//            'code' => Str::random(8),
//        ]);
//        $validated['code'] = Str::random(8);



        // Method 3
//        $classroom = new Classroom($request->all());
//        $classroom->save();

        // Method 4 : here use this method(forceFill()) if not found this parameter in protected $fillable in model.
//        $classroom->forceFill([
//            'code' => Str::random(8)
//        ])->save();

        // PRG Post Redirect Get
        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
//        $classroom = Classroom::find($id);
        $invitation_link = URL::signedRoute('classrooms.join', [
            'classroom' => $classroom->id,
            'code' => $classroom->code,
        ]);

        return view('classrooms.show', [
            'classroom' => $classroom,
            'invitation_link' => $invitation_link,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
//        $classroom = Classroom::find($id);
        return view('classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassroomRequest $request, Classroom $classroom)
    {
        $validated = $request->validated();
//        $classroom = Classroom::find($id);
        $classroom->update($request->all());

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            // Solution 1.
//            $name = $classroom->cover_image_path ?? Str::random(40) . '.' . $file->getClientOriginalExtension();
//            $path = $file->storeAs('public/cover_images', basename($name), [
//                'disk' => 'public'
//            ]);

            // Solution 2.
            $path = $classroom::updateCoverImage($file);
//            $path = Classroom::updateCoverImage($file); // other way to called.

            $request->merge([
                'cover_image' => $path,
            ]);

            $old = $classroom->cover_image_path;
            // Mass Assigment.
            $classroom->update($request->all());

            if ($old && $old != $classroom->cover_image_path) {
                Classroom::deleteCoverImage($old);
            }
        }
        Session::flash('success', 'Classroom updated successfully'); // this's like ->with() method.

        return redirect()->route('classrooms.index');
//            ->with('success', 'Classroom updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        // Method 1
//        $classroom = Classroom::find($id);
        $classroom->delete();
        Classroom::deleteCoverImage($classroom->cover_image_path);
//        Storage::disk(Classroom::$disk)->delete($classroom->cover_image_path);
        // Method 2
//        $count = Classroom::destroy($id); // if was id found will delete and if not found won't delete.

        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom deleted successfully');
    }

    public function forceDelete($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->forceDelete();
        // Classroom::deleteCoverImage($classroom->cover_image_path);

        return to_route('classrooms.trashed')
            ->with('success', "Classroom {{ $classroom->name }} deleted forever!");
    }
}

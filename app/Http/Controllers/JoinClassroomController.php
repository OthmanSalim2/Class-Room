<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\UserClassroomScope;

class JoinClassroomController extends Controller
{
    public function create($id)
    {
        $classroom = Classroom::withoutGlobalScope(UserClassroomScope::class)->findOrFail($id);
        // $classroom = Classroom::findOrFail($id);

        try {
            $this->exists($id, Auth::id());
        } catch (Exception $e) {
            return redirect()->route('classrooms.show', parameters: compact('id'));
        }

        return view('classrooms.join', compact('classroom'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'role' => ['in:student,teacher', 'string'],
        ]);

        $classroom = Classroom::withoutGlobalScope(UserClassroomScope::class)->findOrFail($id);
        // $classroom = Classroom::findOrFail($id);

        try {
            $this->exists($id, Auth::id());
        } catch (Exception $e) {
            return redirect()->route('classrooms.show', parameters: compact('id'));
        }

        DB::table('classroom_user')
        ->insert([
            'classroom_id' => $classroom->id,
            'user_id' => Auth::id(),
            'role' => $request->input('role', 'student'),
            // 'created_at' => date('Y-m-d H:i:s'),
            'created_at' => now(),
        ]);

        return to_route('classrooms.show', $id);
    }

    protected function exists($classroom_id, $user_id)
    {
        $exists = DB::table('classroom_user')
            ->where('classroom_id', $classroom_id)
            ->where('user_id', $user_id)
            ->exists();

        if ($exists) {
            return new Exception('User already exists in this classroom.');
        }
        return new Exception('User does not exist in this classroom.');
    }
}

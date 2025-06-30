<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserClassroomScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // classrooms.id possible make it id only because that is contact with the Classroom Model.
        if($id = Auth::id()) {
            $builder->where('user_id', '=', $id)
                ->orWhereRaw('classrooms.id in (select classroom_id from classroom_user where user_id = ? )',
                [
                    $id,
                ]);
                // this code the same result.
            //     ->orWhereExists(function ($query) use ($id) {
            //         $query->select(DB::raw('1'))
            //         ->from('classroom_user')
            //         ->whereColumn('classroom_id', '=', 'classrooms.id')
            //         ->where('user_id', '=', $id);
            // });
            // ->orWhereRaw('exits (select 1 from classroom_user where classroom_id = classrooms.id and user_id = ? )',
            //     [
            //         $id,
            //     ]);

            // another solution.
            // $builder->where(function (Builder $query) use ($id) {
            //     $query->where('user_id', '=', $id)
            //             ->orWhereExists(function ($query) use ($id) {
            //                 $query->select(DB::raw('1'))
            //                     ->from('classroom_user')
            //                     ->whereColumn('classroom_id', '=', 'classrooms.id')
            //                     ->where('user_id', '=', $id);
            //             });
            //     });
        }
    }
}

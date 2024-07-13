<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkRequest;
use App\Http\Resources\MarkResource;
use App\Models\Mark;
use App\Models\Subject;
use App\Models\SubjectStudent;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class MarkController extends Controller
{

    public function marks(Request $request, Subject $subject, User $student = null)
    {
        Gate::authorize('viewAny', Mark::class);

        $perPage = empty($request->perPage) ? 10 : $request->perPage;

        try {
            
            if (auth()->user()->role->name === 'Student') {
                $student = auth()->user();
            }
            if (empty($student) && in_array(auth()->user()->role->name, ['Parent'])) {
                throw new HttpException(400, 'Student parameter is required.');
            }
            if (auth()->user()->role->name === 'Parent' && auth()->user()->id != $student->parent_id) {
                return response()->json([
                    'message' => 'You do not have access to this resource.'
                ]);
            }

            $marks = Mark::with(['student', 'subject']);
            if (auth()->user()->role->name === 'Admin') {
                $marks->with('insertedBy');
            }

            $marks = $marks->where('subject_id', $subject->id);
            if (!empty($student)) {
                $marks = $marks->where('student_id', $student->id);
            }

            $marks = $marks->paginate($perPage);

            return MarkResource::collection($marks);
        } catch (HttpException $e) {
            throw $e;
        } catch (Throwable $th) {
            Log::channel('err')->error(time() . " There was an error MarkController@marks " . $th->getMessage());

            throw new Exception("There was an error", 500);
        }
    }

    public function store(MarkRequest $request, Subject $subject, User $student)
    {
        Gate::authorize('create', Subject::class);

        if (SubjectStudent::where('subject_id', $subject->id)->where('student_id', $student->id)->exists() === false) {
            return response()->json([
                'message' => 'Student is not enrolled in this subject.'
            ]);
        }
        try {

            $mark = Mark::create([
                'inserted_by' => auth()->user()->id,
                'subject_id'=> $subject->id,
                'student_id'=> $student->id,
                'value'=> $request->value,
            ]);
            $mark->load(['insertedBy', 'subject', 'student']);

            return response()->json([
                'message' => 'Successfully added a mark.',
                'data' => new MarkResource($mark)
            ]);
            
        } catch (\Throwable $th) {
            Log::channel('err')->error(time() . " There was an error MarkController@store " . $th->getMessage());

            throw new Exception("There was an error", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

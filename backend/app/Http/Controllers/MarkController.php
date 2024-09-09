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
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class MarkController extends Controller
{

    public function marksList(Request $request)
    {
        Gate::authorize('viewAny', Mark::class);

        $perPage = empty($request->perPage) ? 10 : $request->perPage;

        try {
            
            if (auth()->user()->role->name === 'Student') {
                
                $student = auth()->user();
            } elseif (auth()->user()->role->name === 'Parent') {
                
                $student = User::where('parent_id', auth()->user()->id)->first();

                
                if (empty($student)) {
                    return response()->json([
                        'message' => 'No student found associated with this parent.'
                    ], 404);
                }
            } else {
                // If the user is neither a student nor a parent, deny access
                return response()->json([
                    'message' => 'You do not have access to this resource.'
                ], 403);
            }

            // Fetch the marks associated with the student
            $marks = Mark::with(['subject'])
            ->where('student_id', $student->id);

            // If the user is an admin, also include the 'insertedBy' relationship
            if (auth()->user()->role->name === 'Admin') {
                $marks->with('insertedBy');
            }

            // Paginate the results
            $marks = $marks->paginate($perPage);

            // Return the marks as a resource collection
            return MarkResource::collection($marks);
        } catch (Throwable $th) {
            // Log any exceptions that occur
            Log::channel('err')->error(time() . " There was an error MarkController@allMarks " . $th->getMessage());

            // Throw a generic exception
            throw new Exception("There was an error", 500);
        }

        // try {

        //     if (auth()->user()->role->name === 'Student') {
        //         $student = auth()->user();
        //     }
        //     if (empty($student) && in_array(auth()->user()->role->name, ['Parent'])) {
        //         throw new HttpException(400, 'Student parameter is required.');
        //     }
        //     if (auth()->user()->role->name === 'Parent' && auth()->user()->id != $student->parent_id) {
        //         return response()->json([
        //             'message' => 'You do not have access to this resource.'
        //         ]);
        //     }

        //     $marks = Mark::with(['student', 'subject']);
        //     if (auth()->user()->role->name === 'Admin') {
        //         $marks->with('insertedBy');
        //     }

        //     $marks = $marks->where('subject_id', $subject->id);
        //     if (!empty($student)) {
        //         $marks = $marks->where('student_id', $student->id);
        //     }

        //     $marks = $marks->paginate($perPage);

        //     return MarkResource::collection($marks);
        // } catch (HttpException $e) {
        //     throw $e;
        // } catch (Throwable $th) {
        //     Log::channel('err')->error(time() . " There was an error MarkController@marks " . $th->getMessage());

        //     throw new Exception("There was an error", 500);
        // }
    }

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
        Gate::authorize('create', Mark::class);

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
    public function update(MarkRequest $request, Mark $mark)
    {
        Gate::authorize('update', $mark);

        try {
            $data = $request->validated();
            
            $mark->fill($data);

            $changes = $mark->getDirty();
            //samo admin moze da mjenja studentov id i subject na ocjeni
            if ((!empty($changes['subject_id']) || !empty($changes['student_id'])) && !$request->user()->isAdmin()) {
                abort(403);
            }

            $mark->save();

            return response()->json(['message' => 'Mark updated successfully.', 'data' => new MarkResource($mark)], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'There was an validation error', 'errors' => $e->errors()], 422);
        } catch (HttpException $e) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error MarkController@update -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mark $mark)
    {
        Gate::authorize('delete', $mark);

        try {
            $mark->delete();

            return response()->json(['message' => 'Mark successfully deleted.'], 200);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error MarkCOntroller@destroy -->' . $th->getMessage());
            return response()->json(['message' => "There was an error with deleting a mark!"], 500);
        }
    }
}

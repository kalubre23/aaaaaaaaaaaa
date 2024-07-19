<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\StudentsSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Models\SubjectStudent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Subject::class);

        try {
            $search = empty($request->search) ? "" : $request->search;
            $search = strtolower(urlencode($search));
            $orderBy = empty($request->orderBy) ? "id" : $request->orderBy;
            $orderDirection = empty($request->orderDirection) ? "DESC" : $request->orderDirection;
            $perPage = empty($request->perPage) ? 10 : $request->perPage;

            $subjects = Subject::with(['teacher']);
            if (!empty($search)) {
                $subjects = $subjects->whereRaw("LOWER(`name`) LIKE ?", ['%' . $search . '%']);
            }

            if (auth()->user()->role->name === 'Teacher') {
                $subjects = $subjects->where('teacher_id', auth()->user()->id);
            }

            $subjects = $subjects->orderBy($orderBy, $orderDirection);
            $subjects = $subjects->paginate($perPage)->withQueryString();

            return SubjectResource::collection($subjects);
        } catch (Throwable $th) {
            Log::channel('err')->error(time() . " There was an error SubjectController@index " . $th->getMessage());

            throw new Exception("There was an error", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        Gate::authorize('create', Subject::class);

        try {
            $data = [
                'name' => $request->name,
                'teacher_id' => $request->teacher_id,
            ];
            if (auth()->user()->role->name === 'Teacher') {
                $data['teacher_id'] = auth()->user()->id;
            }
            $subject = Subject::create($data);
            $subject->load('teacher');

            return response()->json(['message' => "Subject added successfully", 'data' => new SubjectResource($subject) ], 201);
        } catch (ValidationException $e) {

            return response()->json(['message' => 'There was an error', 'errors' => $e->errors()], 422);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error SubjectController@store -->' . $th->getMessage());

            return response()->json(['message' => "There was an error"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        Gate::authorize('view', $subject);
        
        if(empty($subject)) {
            throw new NotFoundHttpException('Subject not found.', null, 404);
        }
        try {
            $subject->load(['teacher']);

            return new SubjectResource($subject);
        } catch (\Exception $e) {
            Log::channel('err')->error('There was an error SubjectController@show -->' . $e->getMessage());
            return response()->json(['message' => 'There was an error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        Gate::authorize('update', $subject);

        try {
            $data = $request->validated();
            $subject->fill($data);

            $changes = $subject->getDirty();
            //samo korisnik moze da mjenja teacher_id na subjectu
            if (!empty($changes['teacher_id']) && !$request->user()->isAdmin()) {
                abort(403);
            }

            $subject->save();

            return response()->json(['message' => 'Subject updated successfully.', 'data' => new SubjectResource($subject)], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'There was an error', 'errors' => $e->errors()], 422);
        } catch (HttpException $e) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error SubjectController@update -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        Gate::authorize('delete', $subject);
        
        try {
            $subject->delete();

            return response()->json(['message' => 'Subject successfully deleted.'], 200);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error SubjectController@destroy -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
    }

    public function add_students(Request $request, Subject $subject)
    {
        // Gate::authorize('add_students', Subject:class);
        Gate::authorize('add_students', $subject);

        $request->validate([
            'students' => ['required', 'regex:/^(\d+,)*\d+$/'],
        ], [
            'students.required' => 'At least one student must be chosen.',
            'students.regex' => 'Student field must be in CSV format.',
        ]);

        $students = User::whereIn('id', explode(',', $request->students))->get();

        $invalidUsers = [];    
        foreach ($students as $user) {
            if (!$user->isStudent()) {
                $invalidUsers[] = $user->id;
            }
        }
        if (!empty($invalidUsers)) {
            throw new HttpException(422, 'Ids: '. implode(', ', $invalidUsers) . ' are not students');
        }
        
        foreach ($students as $student) {
            try {
                SubjectStudent::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id
                ]);
            } catch (Throwable $th) {
                Log::channel('err')->error('There was an error SubjectController@add_students -->' . $th->getMessage());
                return response()->json(['message' => "There was an error"], 500);
            }
        }

        return response()->json(['message' => 'Successfully added students to the subjects.'], 200);
    }

    public function remove_students(Request $request, Subject $subject)
    {
        Gate::authorize('remove_students', Subject::class);

        $request->validate([
            'students' => ['required', 'regex:/^(\d+,)*\d+$/'],
        ], [
            'students.required' => 'At least one student must be chosen.',
            'students.regex' => 'Student field must be in CSV format.',
        ]);

        try {
            SubjectStudent::whereIn('student_id', explode(',', $request->students))->where('subject_id', $subject->id)->delete();

            return response()->json(['message' => 'Successfully removed students from the subject.'], 200);
        } catch (\Throwable $th) {
            Log::channel('err')->error('There was an error SubjectController@remove_students -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
        
    }
}

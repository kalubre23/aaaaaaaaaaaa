<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Subject;
use App\Models\SubjectStudent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UserController extends Controller
{
    public function listStudents($subject_id){
        Gate::authorize('viewAny', User::class);

        try{
            // // Number of users per page
            // $perPage = $request->input('perPage', 10); // Default to 10 if not provided

            // // Query the User model
            // $users = User::where('role_id', 1)
            //     ->paginate($perPage);

            if(auth()->user()->isTeacher()){
                $subject = Subject::where('teacher_id', auth()->user()->id)->first();
    
                if(empty($subject)) {
                    return response()->json(['message' => 'There is no subject that is being taught'], 422);
    
                }
            }


            // $rawData = DB::table('users')
            //     ->join('subject_students', 'users.id', '=', 'subject_students.student_id')
            //     ->where('users.role_id', 1) // Filter for students
            //     ->where('subject_students.subject_id', $subject->id) // Filter for specific subject
            //     ->select('users.*') // Select all columns from the users table
            //     ->get();

            // $students = $rawData->map(function ($item) {
            //     return User::hydrate([$item])->first();
            // });

            // return UserResource::collection($students);
            //return response()->json($students);

            $results = DB::table('subject_students')
            ->select('subject_students.id', 'marks.id AS mark_id', 'users.id AS user_id', 'users.name', 'users.surname', 'marks.value')
            ->leftJoin('marks', function ($join) {
                $join->on('subject_students.student_id', '=', 'marks.student_id')
                ->on('subject_students.subject_id', '=', 'marks.subject_id');
            })
                ->leftJoin('users', 'subject_students.student_id', '=', 'users.id')
                ->where('subject_students.subject_id', '=', $subject_id)
                ->where('users.role_id', '=', 1)
                ->get();

            $students = $results->map(function ($item) {
                return [
                    'id' => $item->id,
                    'mark_id' => $item->mark_id,
                    'user_id' => $item->user_id,
                    'name' => $item->name,
                    'surname' => $item->surname,
                    'mark' => $item->value ? json_decode($item->value) : null // Include the mark or null
                ];
            });

            return response()->json($students);
        } catch (Throwable $th) {
            Log::channel('err')->error(time() . " There was an error UserController@listStudents " . $th->getMessage());

            throw new Exception("There was an error", 500);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        try {
            $search = empty($request->search) ? "" : $request->search;
            $search = strtolower(urlencode($search));
            $orderBy = empty($request->orderBy) ? "id" : $request->orderBy;
            $orderDirection = empty($request->orderDirection) ? "DESC" : $request->orderDirection;
            $perPage = empty($request->perPage) ? 10 : $request->perPage;

            $users = User::with(['role']);
            if (!empty($search)) {
                $users = $users->whereRaw("LOWER(`name`) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`surname`) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`username`) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("LOWER(`email`) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(`name`, '+', `surname`) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(`surname`, '+', `name`) LIKE ?", ['%' . $search . '%']);
            }

            $users = $users->orderBy($orderBy, $orderDirection);
            $users = $users->paginate($perPage)->withQueryString();

            return UserResource::collection($users);
        } catch (Throwable $th) {
            Log::channel('err')->error(time() . " There was an error UserController@index " . $th->getMessage());

            throw new Exception("There was an error", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        try {
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'username' => $request->username,
                'password' => $request->password,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'created_at' => Carbon::now('Europe/Belgrade')
            ]);

            return response()->json(['message' => "User added successfully", 'data' => new UserResource($user) ], 201);
        } catch (ValidationException $e) {

            return response()->json(['message' => 'There was an error', 'errors' => $e->errors()], 422);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error UserController@store -->' . $th->getMessage());

            return response()->json(['message' => "There was an error"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);
        
        if(empty($user)) {
            throw new NotFoundHttpException('User not found.', null, 404);
        }
        try {
            $user->load(['role']);

            //if($user->role == "student" || ($user->role == "parent" && )){

            //}

            return new UserResource($user);
        } catch (\Exception $e) {
            Log::channel('err')->error('There was an error UserController@show -->' . $e->getMessage());
            return response()->json(['message' => 'There was an error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        try {
            $data = $request->validated();
            $user->fill($data);

            $changes = $user->getDirty();
            if (!empty($changes['role_id']) && !$request->user()->isAdmin()) {
                abort(403);
            }
            $user->save();

            return response()->json(['message' => 'User updated successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'There was an error', 'errors' => $e->errors()], 422);
        } catch (HttpException $e) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error UserController@update -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        
        try {
            $user->delete();

            return response()->json(['message' => 'User successfully deleted.'], 200);
        } catch (Throwable $th) {
            Log::channel('err')->error('There was an error UserController@destroy -->' . $th->getMessage());
            return response()->json(['message' => "There was an error"], 500);
        }
    }
}

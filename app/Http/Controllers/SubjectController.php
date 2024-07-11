<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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

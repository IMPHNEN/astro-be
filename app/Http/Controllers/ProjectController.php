<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller {

    public function create(Request $request) {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'project_name' => 'required|string',
            'budget' => 'required|numeric',
            'deadline' => 'required|date',
            'description' => 'required|string|min:100',
            'proposal' => 'required|file:pdf|max:10480',
            'requirements' => 'required|file:pdf|max:10480',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError("Missing required fields", 400, $validator->errors()->all());
        }

        $proposal = Storage::disk('public')->put('proposals', $request->file('proposal'));
        $requirements = Storage::disk('public')->put('requirements', $request->file('requirements'));

        $project = Project::create([
            'slug' => uniqid(),
            'creator_id' => $user->id,
            'project_name' => $request->project_name,
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'deadline' => $request->deadline,
            'proposal' => Storage::url($proposal),
            'requirements' => Storage::url($requirements),
            'status' => 'open'
        ]);

        return $this->respondWithSuccess("Project created successfully", $project);
    }

    public function update(Request $request, $slug) {
        $project = Project::where('slug', $slug)->first();

        if (!$project) return $this->respondWithError("Project not found", 404);

        $validator = Validator::make($request->all(), [
            'project_name' => 'sometimes|string',
            'budget' => 'sometimes|numeric',
            'deadline' => 'sometimes|date',
            'description' => 'sometimes|string|min:100',
            'proposal' => 'sometimes|file:pdf|max:2048',
            'requirements' => 'sometimes|file:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError("Missing required fields", 400, $validator->errors());
        }

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'deadline' => $request->deadline
        ]);

        $project->skills()->sync($request->skills);

        return $this->respondWithSuccess("Project updated successfully");
    }

    public function delete(Request $request, $slug) {
        $project = Project::find($slug);

        if (!$project) return $this->respondWithError("Project not found", 404);
        if ($project->creator_id !== $request->user()->user_id) return $this->respondWithError("Unauthorized", 401);

        $project->delete();

        return $this->respondWithSuccess("Project deleted successfully");
    }

    public function getAll(Request $request) {
        $projects = Project::with([
            'creator',
            'applications',
            'investments',
            'milestones',
            'pitchDecks'
        ])->paginate(20);

        return $this->respondWithData($projects, "Projects fetched successfully", "No projects found");
    }

    public function getOne(Request $request, $slug) {
        $project = Project::with([
            'creator',
            'applications',
            'investments',
            'milestones',
            'pitchDecks'
        ])->where('slug', $slug)->first();

        return $this->respondWithData($project, "Project fetched successfully", "Project not found");
    }

    public function invest(Request $request, $slug) {
        $project = Project::with('creator', 'investments')->where('slug', $slug)->first();

        if (!$project) return $this->respondWithError("Project not found", 404);
        if (Auth::user()->id === $project->creator->id) return $this->respondWithError("You cannot invest in your own project", 400);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->respondWithError("Invalid amount", 400, [
                "investor_id" => $request->user()->id
            ]);
        }

        $investorId = $request->user()->id;
        if (!$investorId) {
            return $this->respondWithError("Investor ID is missing", 400);
        }

        $project->investments()->create([
            'investor_id' => $investorId,
            'amount' => $request->amount,
        ]);

        return $this->respondWithSuccess("Investment made successfully", $project->refresh());
    }

    public function apply(Request $request, $slug) {
        $project = Project::with('creator')->where('slug', $slug)->first();

        if (!$project) return $this->respondWithError("Project not found", 404);
        if (Auth::user()->id === $project->creator->id) return $this->respondWithError("You cannot apply to your own project", 400);

        if ($project->applications()->where('freelancer_id', $request->user()->id)->exists()) {
            return $this->respondWithError("You have already applied to this project", 400);
        }

        $project->applications()->create([
            'freelancer_id' => $request->user()->id,
            'status' => 'pending'
        ]);

        return $this->respondWithSuccess("Application made successfully", $project->refresh());
    }

    private function respondWithData($data, $successMessage, $errorMessage) {
        if (!$data || $data->count() === 0) {
            return response()->json([
                "message" => $errorMessage,
                "success" => false
            ], 404);
        }

        return response()->json([
            "message" => $successMessage,
            "success" => true,
            "data" => $data
        ]);
    }

    private function respondWithError($message, $statusCode, $additionalData = []) {
        return response()->json(
            [
                "message" => $message,
                "success" => false,
                "data" => $additionalData
            ],
            $statusCode
        );
    }

    private function respondWithSuccess($message, $additionalData = []) {
        return response()->json([
            "message" => $message,
            "success" => true,
            "data" => $additionalData
        ]);
    }
}

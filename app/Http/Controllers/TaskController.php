<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function addCategoriesToTask(Request $request, $taskId)
    {
        $task = Task::findOrfail($taskId);
        $task->categories()->attach($request->category_id);
        return response()->json('Category attached successfully', 200);
    }

    public function getTaskCategories($taskId)
    {
        $category = Task::findOrFail($taskId)->categories;
        return response()->json($category, 200);
    }

    public function getCategoriesTasks($categoryId)
    {
        $task = Category::findOrFail($categoryId)->tasks;
        return response()->json($task, 200);
    }
    public function getTaskUser($id)
    {
        $user = Task::findOrFail($id)->user;
        return response()->json($user, 200);
    }

    public function index()
    {
        $tasks = Auth::user()->tasks;
        return response()->json($tasks, 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $user_id = Auth::user()->id;
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        $task = Task::create($validatedData);
        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $user_id = Auth::user()->id;
        $task = Task::findOrFail($id);
        if($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthurized'], 403);
        
        $task->update($request->validated());
        return response()->json($task, 200);
    }

    public function show($id)
    {
        $task = Task::find($id);
        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(null, 204);
    }
}

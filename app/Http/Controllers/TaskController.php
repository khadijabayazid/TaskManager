<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\TaskList\TaskListExtension;

use function Pest\Laravel\json;

class TaskController extends Controller
{
    public function addCategoriesToTask(Request $request, $taskId)
    {
        $task = Task::findOrfail($taskId);
        $user_id = Auth::user()->id;
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthorized'], 403);

        $task->categories()->attach($request->category_id);
        return response()->json('Category attached successfully', 200);
    }

    public function getTaskCategories($taskId)
    {
        $task = Task::findOrFail($taskId);
        $user_id = Auth::user()->id;
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $category = $task->categories;
        return response()->json($category, 200);
    }

    public function getCategoriesTasks($categoryId)
    {
        $task = Category::findOrFail($categoryId)->tasks;
        return response()->json($task, 200);
    }
    public function getTaskUser($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id != Auth::id())
            return response()->json(['message' => 'Unauthorized'], 403);
        $user = $task->user;
        return response()->json($user, 200);
    }

    public function getAllTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
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
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthorized'], 403);

        $task->update($request->validated());
        return response()->json($task, 200);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id != Auth::user()->id)
            return response()->json(['message' => 'Unauthorized'], 403);

        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $user_id = Auth::id();
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $task->delete();
        return response()->json(null, 204);
    }
}

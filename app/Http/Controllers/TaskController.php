<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(){
        $task = Task::all();
        return response()->json($task, 200);
    }

    public function store(Request $request)
    {
        $task = Task::create(
            [
                'title'=>$request->title,
                'description'=>$request->description,
                'priority'=>$request->priority
            ]
        );
        return response()->json($task,201);
    }

    public function update(Request $request, $id){
        $task = Task::findOrFail($id);
        $task->update($request->only('title','description','priority'));
        return response()->json($task,200);
    }

    public function show($id){
        $task = Task::find($id);
        return response()->json($task, 200);
    }

    public function destroy($id){
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(null, 204);
    }


}

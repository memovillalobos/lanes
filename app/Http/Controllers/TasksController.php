<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Task;

class TasksController extends Controller
{
    //

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'required|numeric',
            'status' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'messages' => $validator->errors()->all(),
                'data' => []
            ], 422);
        }

        $tasks = Task::where('student_number', $request->student_number)->where('status', $request->status)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'stauts' => 'ok',
            'code' => 200,
            'messages' => ['Task list was successfully retrieved'],
            'data' => [
                'tasks' => $tasks
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'required|numeric',
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'messages' => $validator->errors()->all(),
                'data' => []
            ], 422);
        }

        $task = Task::create($request->only(['student_number', 'name', 'description'])+['status' => 1]);
        return response()->json([
            'stauts' => 'ok',
            'code' => 201,
            'messages' => ['Task list was successfully created'],
            'data' => [
                'task' => $task
            ]
        ], 200);
    }

    public function update(Task $task, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'required|numeric',
            'status' => 'required|in:1,2,3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'messages' => $validator->errors()->all(),
                'data' => []
            ], 422);
        }

        if ($task->student_number != $request->student_number) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'messages' => ['This task does not belong to the provided student number'],
                'data' => []
            ], 422);
        }

        $task->update($request->only(['status']));
        return response()->json([
            'stauts' => 'ok',
            'code' => 201,
            'messages' => ['Task was successfully updated'],
            'data' => [
                'task' => $task->fresh()
            ]
        ], 200);
    }

    public function empty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'messages' => $validator->errors()->all(),
                'data' => []
            ], 422);
        }

        Task::where('student_number', $request->student_number)->where('status', 3)->whereNull('hidden_at')->update([
            'hidden_at' => \Carbon\Carbon::now()
        ]);
        return response()->json([
            'stauts' => 'ok',
            'code' => 200,
            'messages' => ['Completed tasks were successfully cleared'],
            'data' => []
        ], 200);
    }
}

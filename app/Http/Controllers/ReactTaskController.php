<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

//
class ReactTaskController extends Controller
{
    /**************************************
     *
     **************************************/
    public function index()
    {
//var_dump("#index");
        $tasks = Task::orderBy('id', 'desc')->paginate(10 );
        return view('react/tasks/index')->with('tasks', $tasks);
    }    
    /**************************************
     *
     **************************************/
    public function create()
    {
// var_dump("#create");
        return view('react/tasks/create')->with('task', new Task());
    }
    /**************************************
     *
     **************************************/
    public function show($id)
    {
        $task = Task::find($id);
        return view('react/tasks/show')->with('task_id', $id );
    }
    /**************************************
     *
     **************************************/
    public function edit($id)
    {
        $task = Task::find($id);
        return view('react/tasks/edit')->with('task_id', $id);
    }    


}

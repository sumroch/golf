<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Hole;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.grandmaster.course', [
            'courses' => Course::orderBy('name', 'asc')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('admin.grandmaster.course-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->safe()->only(['name', 'location', 'par', 'total_holes']));
        
        $course->holes()->createMany(
            collect($request->holes)->map(function ($holeData, $index) {
                return [
                    'number' => $index,
                    'par' => $holeData['par'],
                    'allowed_time' => '00:' . str_pad($holeData['allowed_time'], 2, '0', STR_PAD_LEFT) . ':00',
                ];
            })->toArray()
        );

        return redirect()->route('course.index');
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $course = Course::where('id', $id)->with('holes')->first();

        $course->holes->map(function ($hole) {
            $hole->allowed_time = date('i', strtotime($hole->allowed_time));
            return $hole;
        });

        return view('admin.grandmaster.course-show', [
            'data' => $course,
            'holes' => $course->holes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->safe()->only(['name', 'location', 'par', 'total_holes']));

        foreach ($request->holes as $holeData) {
            $hole = Hole::find($holeData['id']);
            if ($hole) {
                $hole->update([
                    'par' => $holeData['par'],
                    'allowed_time' => '00:' . str_pad($holeData['allowed_time'], 2, '0', STR_PAD_LEFT) . ':00',
                ]);
            }
        }

        return redirect()->route('course.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->holes()->delete();
        $course->delete();

        return redirect()->route('course.index');
    }
}

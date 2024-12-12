<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PerformanceReviewController extends Controller
{
    public function index()
    {
        $reviews = PerformanceReview::with(['employee', 'reviewedBy'])->get();

        return Inertia::render('PerformanceReviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function create()
    {
        $employees = Employee::all();

        return Inertia::render('PerformanceReviews/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewed_by' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'score' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string',
        ]);

        PerformanceReview::create($data);

        return Inertia::location(route('performance_reviews.index'));
    }

    public function edit(PerformanceReview $review)
    {
        $employees = Employee::all();

        return Inertia::render('PerformanceReviews/Edit', [
            'review' => $review,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, PerformanceReview $review)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewed_by' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'score' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string',
        ]);

        $review->update($data);

        return Inertia::location(route('performance_reviews.index'));
    }

    public function show(PerformanceReview $review)
    {
        $review->load(['employee', 'reviewedBy']);

        return Inertia::render('PerformanceReviews/Show', [
            'review' => $review,
        ]);
    }
}

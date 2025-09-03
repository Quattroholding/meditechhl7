<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function index()
    {
        return view('surveys.index');
    }

    public function create()
    {
        return view('surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $survey = Survey::create([
            'title' => $request->title,
            'description' => $request->description,
            'client_id' => 1,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('surveys.show', $survey->id)
            ->with('success', 'Encuesta creada exitosamente.');
    }

    public function show(Survey $survey)
    {
        $survey->load(['questions' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:draft,active,inactive',
        ]);

        $survey->update($request->only(['title', 'description', 'is_active', 'status']));

        return redirect()->route('surveys.show', $survey->id)
            ->with('success', 'Encuesta actualizada exitosamente.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('surveys.index')
            ->with('success', 'Encuesta eliminada exitosamente.');
    }

    public function publicForm(string $token)
    {
        $surveyResponse = SurveyResponse::where('token', $token)->firstOrFail();
        $survey = $surveyResponse->survey->load(['questions' => function ($query) {
            $query->orderBy('order');
        }]);

        if ($surveyResponse->isCompleted()) {
            return view('surveys.completed', compact('surveyResponse'));
        }

        return view('surveys.public', compact('survey', 'surveyResponse'));
    }

    public function submitPublic(Request $request, string $token)
    {
        $surveyResponse = SurveyResponse::where('token', $token)->firstOrFail();

        if ($surveyResponse->isCompleted()) {
            return redirect()->route('survey.public', $token)
                ->with('error', 'Esta encuesta ya ha sido completada.');
        }

        $surveyResponse->update([
            'responses' => $request->responses,
            'status' => 'completed',
            'submitted_at' => now(),
        ]);

        return redirect()->route('survey.public', $token)
            ->with('success', 'Encuesta enviada exitosamente. ¡Gracias por su participación!');
    }
}

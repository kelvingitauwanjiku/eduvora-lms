<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course\Quiz;
use App\Models\Course\QuizQuestion;
use App\Models\Course\QuizSubmission;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_mark' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $quiz = Quiz::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Quiz created successfully',
            'data' => $quiz,
        ], 201);
    }

    public function show(int $id)
    {
        $quiz = Quiz::with(['section.course', 'questions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $quiz,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'pass_mark' => 'integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $quiz->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Quiz updated successfully',
            'data' => $quiz,
        ]);
    }

    public function destroy(int $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz deleted successfully',
        ]);
    }

    public function questions(int $quizId)
    {
        $questions = QuizQuestion::where('quiz_id', $quizId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,fill_blank,short_answer',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
        ]);

        $question = QuizQuestion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully',
            'data' => $question,
        ], 201);
    }

    public function updateQuestion(Request $request, int $id)
    {
        $question = QuizQuestion::findOrFail($id);

        $validated = $request->validate([
            'question' => 'string',
            'question_type' => 'in:multiple_choice,true_false,fill_blank,short_answer',
            'options' => 'nullable|array',
            'correct_answer' => 'string',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully',
            'data' => $question,
        ]);
    }

    public function deleteQuestion(int $id)
    {
        $question = QuizQuestion::findOrFail($id);
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully',
        ]);
    }

    public function sortQuestions(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:quiz_questions,id',
            'questions.*.order' => 'required|integer',
        ]);

        foreach ($request->input('questions') as $questionData) {
            QuizQuestion::where('id', $questionData['id'])->update(['order' => $questionData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Questions sorted successfully',
        ]);
    }

    public function results(Request $request, int $quizId)
    {
        $query = QuizSubmission::with('user')
            ->where('quiz_id', $quizId);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    public function resultPreview(int $submissionId)
    {
        $submission = QuizSubmission::with(['user', 'quiz.questions', 'answers'])
            ->findOrFail($submissionId);

        return response()->json([
            'success' => true,
            'data' => $submission,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Course\Course;
use App\Models\Course\Section;
use App\Models\Course\Lesson;

class AIController extends Controller
{
    protected $openAIKey;
    protected $model;

    public function __construct()
    {
        $this->openAIKey = config('services.openai.key', env('OPENAI_API_KEY'));
        $this->model = config('services.openai.model', 'gpt-4');
    }

    public function generateCourseContent(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'type' => 'required|in:outline,description,syllabus',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'language' => 'nullable|string|max:10',
        ]);

        $topic = $request->input('topic');
        $type = $request->input('type');
        $level = $request->input('level', 'intermediate');
        $language = $request->input('language', 'en');

        $prompt = $this->buildContentPrompt($topic, $type, $level, $language);
        
        try {
            $response = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'topic' => $topic,
                    'level' => $level,
                    'generated_content' => $response,
                ],
                'tokens_used' => $this->estimateTokens($response),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateLessonContent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'section_id' => 'nullable|integer',
            'type' => 'required|in:video,text,quiz,assignment',
            'description' => 'nullable|string|max:1000',
        ]);

        $prompt = "Generate lesson content for: {$request->input('title')}\n";
        $prompt .= "Type: {$request->input('type')}\n";
        
        if ($request->has('description')) {
            $prompt .= "Description: {$request->input('description')}\n";
        }

        $prompt .= "\nGenerate: 1. Learning objectives 2. Key points to cover 3. Suggested duration 4. Content structure";
        
        try {
            $response = $this->callOpenAI($prompt);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $response,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateQuiz(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|integer',
            'question_count' => 'nullable|integer|min:1|max:20',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $lesson = Lesson::findOrFail($request->input('lesson_id'));
        $questionCount = $request->input('question_count', 5);
        $difficulty = $request->input('difficulty', 'medium');

        $prompt = "Generate {$questionCount} multiple choice quiz questions for: {$lesson->title}\n";
        $prompt .= "Difficulty: {$difficulty}\n";
        $prompt .= "\nFormat as JSON with: question, options (a,b,c,d), correct_answer, explanation";

        try {
            $response = $this->callOpenAI($prompt);
            $questions = $this->parseQuizResponse($response);

            return response()->json([
                'success' => true,
                'data' => [
                    'questions' => $questions,
                    'lesson_id' => $lesson->id,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function improveContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'improvement_type' => 'required|in:grammar,tone,engagement,clarity,seo',
            'target_audience' => 'nullable|string',
        ]);

        $content = $request->input('content');
        $improvementType = $request->input('improvement_type');
        $targetAudience = $request->input('target_audience', 'general');

        $prompt = "Improve this content for {$improvementType}:\n\n{$content}\n\n";
        $prompt .= "Target audience: {$targetAudience}\n";
        $prompt .= "Provide the improved version.";

        try {
            $response = $this->callOpenAI($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original' => $content,
                    'improved' => $response,
                    'improvement_type' => $improvementType,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function translateContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'target_language' => 'required|string|size:2',
        ]);

        $content = $request->input('content');
        $targetLanguage = $request->input('target_language');

        $languages = [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
        ];

        $languageName = $languages[$targetLanguage] ?? $targetLanguage;

        $prompt = "Translate to {$languageName}:\n\n{$content}";

        try {
            $response = $this->callOpenAI($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original' => $content,
                    'translated' => $response,
                    'target_language' => $targetLanguage,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateSeo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'keywords' => 'nullable|array',
        ]);

        $title = $request->input('title');
        $description = $request->input('description', '');
        $keywords = $request->input('keywords', []);

        $prompt = "Generate SEO metadata for:\n";
        $prompt .= "Title: {$title}\n";
        
        if ($description) {
            $prompt .= "Description: {$description}\n";
        }
        
        if ($keywords) {
            $prompt .= "Keywords: " . implode(', ', $keywords) . "\n";
        }

        $prompt .= "\nProvide: 1. Meta title (max 60 chars) 2. Meta description (max 160 chars) 3. SEO keywords 4. Schema.org JSON-LD code";

        try {
            $response = $this->callOpenAI($prompt);
            $seo = $this->parseSeoResponse($response);

            return response()->json([
                'success' => true,
                'data' => $seo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateSummary(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'max_length' => 'nullable|integer|min:50|max:500',
        ]);

        $content = $request->input('content');
        $maxLength = $request->input('max_length', 200);

        $prompt = "Summarize in {$maxLength} words or less:\n\n{$content}";

        try {
            $response = $this->callOpenAI($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $response,
                    'word_count' => str_word_count($response),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'context' => 'nullable|string',
        ]);

        $message = $request->input('message');
        $context = $request->input('context', '');
        $userId = $request->user()?->id;

        $systemPrompt = "You are an AI teaching assistant for an e-learning platform. ";
        $systemPrompt .= "Help students understand concepts, answer questions about courses, ";
        $systemPrompt .= "and provide educational guidance.\n\n";

        if ($context) {
            $systemPrompt .= "Context: {$context}\n";
        }

        try {
            $response = $this->callOpenAI($message, $systemPrompt);

            Cache::put("ai_chat_user_{$userId}", [
                'message' => $message,
                'response' => $response,
                'timestamp' => now()->toIso8601String(),
            ], now()->addHours(24));

            return response()->json([
                'success' => true,
                'data' => [
                    'response' => $response,
                    'message' => $message,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function callOpenAI(string $userPrompt, string $systemPrompt = null): string
    {
        if (!$this->openAIKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        $messages = [];
        
        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        } else {
            $messages[] = [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant for an e-learning platform.'
            ];
        }
        
        $messages[] = ['role' => 'user', 'content' => $userPrompt];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->openAIKey}",
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();
        
        return $data['choices'][0]['message']['content'] ?? '';
    }

    protected function buildContentPrompt(string $topic, string $type, string $level, string $language): string
    {
        $prompts = [
            'outline' => "Create a comprehensive course outline for: {$topic}\nLevel: {$level}\nLanguage: {$language}\n\nInclude: Course overview, Learning objectives, Module breakdown, Assessment criteria",
            'description' => "Write an engaging course description for: {$topic}\nLevel: {$level}\nTarget audience: {$language}\n\nInclude: What students will learn, Prerequisites, Course structure, Why take this course",
            'syllabus' => "Create a detailed syllabus for: {$topic}\nLevel: {$level}\nFormat: Weekly breakdown\n\nInclude: Weekly topics, Learning materials, Assignments, Grading policy",
        ];

        return $prompts[$type] ?? $prompts['outline'];
    }

    protected function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / 4);
    }

    protected function parseQuizResponse(string $response): array
    {
        if (preg_match('/\[.*\]/s', $response, $matches)) {
            return json_decode($matches[0], true) ?? [];
        }
        
        return [
            ['question' => $response, 'error' => 'Parse error']
        ];
    }

    protected function parseSeoResponse(string $response): array
    {
        return [
            'meta_title' => '',
            'meta_description' => '',
            'keywords' => [],
            'schema' => '{}',
            'raw' => $response,
        ];
    }
}
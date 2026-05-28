<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Trade;
use App\Models\DailyRitual;
use App\Models\AiInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AiCoachController extends Controller
{
    public function index()
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        $insights = AiInsight::where('user_id', $profile->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ai.index', compact('insights'));
    }

    public function generate(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        // 1. Gather Context (Last 7 Days)
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        $recentTrades = Trade::where('user_id', $profile->id)
            ->whereNotNull('exit_price')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->get();
            
        $recentRituals = DailyRitual::where('user_id', $profile->id)
            ->where('date', '>=', $sevenDaysAgo->format('Y-m-d'))
            ->get();

        // Calculate basic stats for context
        $totalTrades = $recentTrades->count();
        $wins = $recentTrades->where('pnl', '>', 0)->count();
        $winRate = $totalTrades > 0 ? round(($wins / $totalTrades) * 100) : 0;
        $netPnl = $recentTrades->sum('pnl');

        // Extract commonly used tags and their performance
        $tagsData = [];
        foreach($recentTrades as $trade) {
            foreach($trade->tags ?? [] as $tag) {
                if(!isset($tagsData[$tag])) {
                    $tagsData[$tag] = ['count' => 0, 'pnl' => 0];
                }
                $tagsData[$tag]['count']++;
                $tagsData[$tag]['pnl'] += $trade->pnl;
            }
        }

        // Format data to JSON string for the prompt
        $contextData = [
            'period' => 'Last 7 Days',
            'net_pnl' => $netPnl,
            'win_rate' => $winRate . '%',
            'total_trades' => $totalTrades,
            'tags_performance' => $tagsData,
            'recent_rituals' => $recentRituals->map(function($r) {
                return [
                    'date' => $r->date,
                    'mood' => $r->mood,
                    'sleep_quality' => $r->sleep_quality,
                    'followed_plan' => $r->followed_plan,
                    'notes' => $r->notes
                ];
            })->toArray()
        ];
        
        $contextString = json_encode($contextData, JSON_PRETTY_PRINT);

        // 2. Build the System Prompt
        $systemPrompt = "Bertindaklah sebagai Pelatih Psikologi Trading yang elit, galak tapi suportif. Ini adalah data trading dari muridmu selama 7 hari terakhir.\n\nAnalisis korelasi antara kualitas tidur/mood dengan hasil Profit/Loss. Penting: Jika data psikologi/jurnal kosong, tegur keras kemalasannya dalam melakukan pencatatan.\n\nIdentifikasi kebiasaan buruk atau blind spot dari data eksekusinya (misal: overtrading, keengganan mengambil risiko/analysis paralysis, atau memaksakan setup).\n\nBerikan maksimal 3 rekomendasi taktis berbasis action-plan yang wajib dia eksekusi minggu depan.\n\nWajib jawab menggunakan Bahasa Indonesia dengan gaya bicara yang asertif dan to the point.";

        // 3. Call Gemini API
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            return back()->with('error', 'Gemini API Key is missing in .env');
        }

        // We use withoutVerifying() to prevent local cURL SSL errors in Windows
        $response = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nHere is my trading and lifestyle data for the last 7 days:\n" . $contextString]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
            ]
        ]);

        if ($response->successful()) {
            $insightText = $response->json('candidates.0.content.parts.0.text');
            
            // 4. Save to Database
            AiInsight::create([
                'user_id' => $profile->id,
                'context_data' => $contextData,
                'insight_text' => $insightText,
            ]);

            return back()->with('success', 'AI Coach has generated a new insight!');
        }

        return back()->with('error', 'Failed to generate AI insight: ' . $response->body());
    }
}

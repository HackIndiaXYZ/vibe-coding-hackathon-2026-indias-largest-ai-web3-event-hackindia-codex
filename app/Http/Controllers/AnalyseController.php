<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyseController extends Controller
{
    public function analyse(Request $request)
    {
        if (auth()->check()) {

            $user = auth()->user();

            if ($user->investigations_left <= 0) {

                return response()->json([

                    'status' => false,

                    'limit_reached' => true
                ]);
            }

            $user->investigations_left--;

            $user->save();
        }
        $url = $request->url;
        $title = $request->title;
        $context = $request->context;

        // =========================
        // CHECK YOUTUBE URL
        // =========================

        if (
            str_contains($url, 'youtube.com') ||
            str_contains($url, 'youtu.be')
        ) {

            // =========================
            // BLOCK POSTS
            // =========================

            if (str_contains($url, '/post/')) {

                return response()->json([
                    'status' => false,
                    'message' => 'YouTube posts not supported yet'
                ]);
            }

            // =========================
            // EXTRACT VIDEO ID
            // =========================

            preg_match(
                '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\n?#]+)/',
                $url,
                $matches
            );

            $videoId = $matches[1] ?? null;

            if (!$videoId) {

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid YouTube URL'
                ]);
            }

            // =========================
            // FETCH VIDEO DATA
            // =========================

            $response = Http::get(
                'https://www.googleapis.com/youtube/v3/videos',
                [
                    'part' => 'snippet,statistics,contentDetails',
                    'id' => $videoId,
                    'key' => env('YOUTUBE_API_KEY')
                ]
            );

            $data = $response->json();

            if (empty($data['items'])) {

                return response()->json([
                    'status' => false,
                    'message' => 'Video not found'
                ]);
            }

            $video = $data['items'][0];

            // =========================
            // VIDEO INFO
            // =========================

            $videoTitle =
                $video['snippet']['title'];

            $videoChannel =
                $video['snippet']['channelTitle'];

            $videoThumbnail =
                $video['snippet']['thumbnails']['high']['url'];

            $videoViews =
                $video['statistics']['viewCount'] ?? 0;

            $videoDuration =
                $video['contentDetails']['duration'];

            $videoPublishedAt =
                $video['snippet']['publishedAt'];

            // =========================
            // USER TITLE MATCH
            // =========================

            similar_text(
                strtolower($title),
                strtolower($videoTitle),
                $percent
            );

            // =========================
            // SEARCH SIMILAR VIDEOS
            // =========================

            $searchResponse = Http::get(
                'https://www.googleapis.com/youtube/v3/search',
                [
                    'part' => 'snippet',
                    'q' => $videoTitle,
                    'type' => 'video',
                    'maxResults' => 15,
                    'key' => env('YOUTUBE_API_KEY')
                ]
            );

            $searchData =
                $searchResponse->json();

            $possibleMatches = [];

            // =========================
            // PROCESS MATCHES
            // =========================

            if (!empty($searchData['items'])) {

                foreach ($searchData['items'] as $item) {

                    // Skip same video
                    if (
                        isset($item['id']['videoId']) &&
                        $item['id']['videoId'] == $videoId
                    ) {
                        continue;
                    }

                    $candidateTitle =
                        $item['snippet']['title'];

                    $candidateThumbnail =
                        $item['snippet']['thumbnails']['high']['url'] ?? null;

                    // =========================
                    // THUMBNAIL MATCH
                    // =========================

                    $thumbnailMatch =
                        $candidateThumbnail == $videoThumbnail;

                    // =========================
                    // TITLE SIMILARITY
                    // =========================

                    similar_text(
                        strtolower($videoTitle),
                        strtolower($candidateTitle),
                        $similarity
                    );

                    // =========================
                    // FILTER GOOD MATCHES
                    // =========================

                    if (
                        $similarity > 50 ||
                        $thumbnailMatch
                    ) {

                        $possibleMatches[] = [

                            'title' =>
                            $candidateTitle,

                            'channel' =>
                            $item['snippet']['channelTitle'],

                            'video_id' =>
                            $item['id']['videoId'] ?? null,

                            'thumbnail' =>
                            $candidateThumbnail,

                            'published_at' =>
                            $item['snippet']['publishedAt'],

                            'similarity' =>
                            round($similarity, 2),

                            'thumbnail_match' =>
                            $thumbnailMatch,
                        ];
                    }
                }
            }

            // =========================
            // SORT MATCHES
            // =========================

            usort($possibleMatches, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });

            // =========================
            // DETECT OLDEST VIDEO
            // =========================

            $oldestVideo = null;

            foreach ($possibleMatches as $match) {

                if (
                    !$oldestVideo ||
                    strtotime($match['published_at']) <
                    strtotime($oldestVideo['published_at'])
                ) {

                    $oldestVideo = $match;
                }
            }

            // =========================
            // FINAL RESULT LOGIC
            // =========================

            if (
                $percent > 70 &&
                count($possibleMatches) == 0
            ) {

                $result = 'Verified';
            } elseif (
                count($possibleMatches) > 0
            ) {

                $result = 'Possible Reupload';
            } else {

                $result = 'Misleading Claim';
            }

            // =========================
            // FINAL RESPONSE
            // =========================

            return response()->json([

                'status' => true,

                'youtube_data' => [

                    'title' =>
                    $videoTitle,

                    'channel' =>
                    $videoChannel,

                    'thumbnail' =>
                    $videoThumbnail,

                    'views' =>
                    $videoViews,

                    'duration' =>
                    $videoDuration,

                    'published_at' =>
                    $videoPublishedAt,
                ],

                'analysis' => [

                    'user_title' =>
                    $title,

                    'match_percentage' =>
                    round($percent, 2),

                    'result' =>
                    $result
                ],

                'possible_original_source' =>
                $oldestVideo,

                'possible_matches' =>
                $possibleMatches
            ]);
        }

        // =========================
        // X / TWITTER SUPPORT
        // =========================

        if (
            str_contains($url, 'x.com') ||
            str_contains($url, 'twitter.com')
        ) {

            // =========================
            // CONVERT URL FOR DISPLAY
            // =========================

            $fixedUrl = str_replace(
                ['x.com', 'twitter.com'],
                'twitter.com',
                $url
            );

            // =========================
            // FETCH TWEET VIA OEMBED
            // =========================

            $oembedUrl = 'https://publish.twitter.com/oembed?url=' . urlencode($url);

            try {

                $response = Http::timeout(10)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0'
                ])->get($oembedUrl);

                if (!$response->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Could not fetch tweet.'
                    ]);
                }

                $data = $response->json();

                // Extract tweet text from embed HTML
                $html = $data['html'] ?? '';
                preg_match('/<p[^>]*>(.*?)<\/p>/s', $html, $pMatch);
                $tweetTitle = strip_tags($pMatch[1] ?? 'Unknown Tweet');

                // Remove pic.twitter links
                $tweetTitle = preg_replace(
                    '/pic\.twitter\.com\/\S+/',
                    '',
                    $tweetTitle
                );

                // Remove extra spaces
                $tweetTitle = trim($tweetTitle);

                // Extract image from embed HTML
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $html, $imgMatch);
                $tweetImage = $imgMatch[1] ?? null;

                // Fallback to oembed thumbnail
                if (!$tweetImage) {
                    $tweetImage = $data['thumbnail_url'] ?? null;
                }

                // Google reverse image search URL
                $reverseSearchUrl = $tweetImage
                    ? 'https://www.google.com/searchbyimage?image_url=' . urlencode($tweetImage)
                    : null;
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request failed: ' . $e->getMessage()
                ]);
            }

            // =========================
            // GROQ AI ANALYSIS
            // =========================

            try {

                $groqResponse = Http::timeout(15)->withHeaders([
                    'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => 'llama3-8b-8192',
                    'max_tokens' => 300,
                    'messages'   => [
                        [
                            'role'    => 'system',
                            'content' => 'You are a misinformation detection expert. Analyze claims against actual content and respond only in the exact format requested.'
                        ],
                        [
                            'role'    => 'user',
                            'content' => "User submitted this claim: \"{$title}\"\nActual tweet content: \"{$tweetTitle}\"\n\nAnalyze if the claim accurately represents the tweet. Also consider if this content might be recycled, reposted, or taken out of context.\nReply in this exact format:\nVERDICT: VERIFIED or MISLEADING\nSCORE: a number from 0 to 100 representing match confidence\nREASON: one sentence explanation\nREUPLOAD_RISK: LOW or MEDIUM or HIGH\nREUPLOAD_REASON: one sentence about whether this looks like recycled or old content"
                        ]
                    ]
                ]);

                $groqData  = $groqResponse->json();
                $groqText  = $groqData['choices'][0]['message']['content'] ?? '';

                // Parse response
                preg_match('/VERDICT:\s*(VERIFIED|MISLEADING)/i', $groqText, $verdictMatch);
                preg_match('/SCORE:\s*(\d+)/i', $groqText, $scoreMatch);
                preg_match('/REASON:\s*(.+)/i', $groqText, $reasonMatch);

                $result       = isset($verdictMatch[1]) ? ucfirst(strtolower($verdictMatch[1])) : 'Misleading';
                // REAL similarity score
                similar_text(
                    strtolower($title),
                    strtolower($tweetTitle),
                    $realPercent
                );

                $percent = round($realPercent, 2);
                $groqReason   = trim($reasonMatch[1] ?? 'Analysis inconclusive.');
                preg_match('/REUPLOAD_RISK:\s*(LOW|MEDIUM|HIGH)/i', $groqText, $reuploadMatch);
                preg_match('/REUPLOAD_REASON:\s*(.+)/i', $groqText, $reuploadReasonMatch);
                $reuploadRisk   = $reuploadMatch[1] ?? 'LOW';
                $reuploadReason = trim($reuploadReasonMatch[1] ?? '');
            } catch (\Exception $e) {

                // Fallback to title match if Groq fails
                similar_text(strtolower($title), strtolower($tweetTitle), $percent);
                $result         = $percent > 70 ? 'Verified' : 'Misleading';
                $groqReason     = 'AI analysis unavailable. Falling back to title match.';
                $reuploadRisk   = 'LOW';
                $reuploadReason = '';
            }

            // =========================
            // RESPONSE
            // =========================

            return response()->json([

                'status' => true,

                'platform' => 'X',

                'tweet_data' => [
                    'title'              => $tweetTitle,
                    'image'              => $tweetImage,
                    'source_url'         => $fixedUrl,
                    'reverse_search_url' => $reverseSearchUrl,
                ],

                'analysis' => [
                    'user_title'       => $title,
                    'match_percentage' => round($percent, 2),
                    'result'           => $result,
                    'reason'           => $groqReason,
                    'reupload_risk'    => $reuploadRisk,
                    'reupload_reason'  => $reuploadReason,
                ]
            ]);
        }

        // =========================
        // UNKNOWN PLATFORM
        // =========================

        return response()->json([
            'status' => false,
            'message' => 'Unknown Platform'
        ]);
    }
}

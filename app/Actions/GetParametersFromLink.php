<?php
namespace App\Actions;

class GetParametersFromLink
{
    public static function execute($link): array
    {
        $videoId = self::extractVideoId($link);
        $url = "https://www.youtube.com/watch?v=" . $videoId;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception("Erro ao acessar o YouTube: " . curl_error($ch));
        }

        curl_close($ch);

        if (!preg_match('/<title>(.+?) - YouTube<\/title>/', $response, $titleMatches)) {
            throw new \Exception("Não foi possível encontrar o título do vídeo");
        }

        $title = html_entity_decode($titleMatches[1], ENT_QUOTES);

        if (preg_match('/"viewCount":\s*"(\d+)"/', $response, $viewMatches)) {
            $views = (int)$viewMatches[1];
        } elseif (preg_match('/"viewCount"\s*:\s*{[^}]*"simpleText"\s*:\s*"([\d,\.]+)"/', $response, $viewMatches)) {
            $views = (int)str_replace(['.', ','], '', $viewMatches[1]);
        } else {
            $views = 0;
        }

        return [
            'title' => $title,
            'views' => $views,
            'youtube_id' => $videoId,
            'thumb' => 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg',
        ];
    }

    public static function extractVideoId($link)
    {
        $videoId = null;
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $link, $matches)) {
                $videoId = $matches[1];
                break;
            }
        }
        return $videoId;
    }
}
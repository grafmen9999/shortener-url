<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShortUrl;

class ShortenerUrlController extends Controller
{
    /**
     * @var \App\Services\ShortUrl
     */
    protected $serviceShortUrl;

    public function __construct()
    {
        $this->serviceShortUrl = new ShortUrl();
    }

    public function createShortUrl(Request $request)
    {
        try {
            return response()->json([
                'status' => 200,
                'short_code' => $this->serviceShortUrl->urlToShortCode($request->get('url'))
            ]);
        } catch (\App\Exceptions\BadRequest $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function findShortUrl(Request $request, string $code)
    {
        try {
            $url = $this->serviceShortUrl->shortCodeToUrl($code);

            return response()->redirectTo($url);
        } catch (\App\Exceptions\BadRequest $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        }
    }
}

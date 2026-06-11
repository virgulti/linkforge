<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ShortCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function __construct(private readonly ShortCodeGenerator $generator) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return LinkResource::collection(
            Link::where('user_id', Auth::id())
                ->withCount('clicks')
                ->latest()
                ->paginate(20)
                ->appends($request->query())
        );
    }

    public function store(StoreLinkRequest $request): JsonResponse
    {
        try {
            $link = $this->generator->createLink(
                $request->input('url'),
                Auth::id(),
                $request->input('custom_code'),
                $request->date('expires_at'),
            );
        } catch (ShortCodeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LinkResource($link))->response()->setStatusCode(201);
    }

    public function show(Link $link): LinkResource
    {
        if ($link->user_id !== Auth::id()) {
            abort(404);
        }

        return new LinkResource($link->loadCount('clicks'));
    }

    public function destroy(Link $link): \Illuminate\Http\Response
    {
        if ($link->user_id !== Auth::id()) {
            abort(404);
        }

        $link->delete();

        return response()->noContent();
    }
}

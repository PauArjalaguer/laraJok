<?php

namespace App\Http\Controllers;

use App\Models\Merchandisings;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoChannel;
use App\Services\YoutubeVideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Public page listing YouTube videos with filters and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $channelId = $request->input('channel_id');
        $dateFilter = $request->input('date');

        $videos = Video::with('channel')
            ->search($search)
            ->byChannel($channelId)
            ->byDateFilter($dateFilter)
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $channels = VideoChannel::where('is_active', true)->orderBy('name')->get();
        $userSavedData = User::userSavedData();
        $merchandisingList = Merchandisings::merchandisingReturnFiveRandomItems();

        return view('videos', compact('videos', 'channels', 'search', 'channelId', 'dateFilter', 'userSavedData', 'merchandisingList'));
    }

    /**
     * Admin dashboard for managing channels and video sync.
     */
    public function dashboard()
    {
        $channels = VideoChannel::withCount('videos')->get();
        $recentVideos = Video::with('channel')->orderBy('published_at', 'desc')->paginate(20);

        return view('dashboard_videos', compact('channels', 'recentVideos'));
    }

    /**
     * Store or update a video channel/playlist in dashboard.
     */
    public function storeChannel(Request $request, YoutubeVideoService $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:channel,playlist',
            'identifier' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
        ]);

        $channel = VideoChannel::updateOrCreate(
            ['identifier' => $validated['identifier']],
            [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'url' => $validated['url'] ?? null,
                'playlist_id' => $validated['type'] === 'playlist' ? $validated['identifier'] : null,
                'is_active' => true,
            ]
        );

        $syncedCount = $service->syncChannel($channel);

        return redirect()->route('dashboard.videos')->with('status', "Canal desat i {$syncedCount} vídeos sincronitzats.");
    }

    /**
     * Delete a channel.
     */
    public function deleteChannel($id)
    {
        VideoChannel::where('id', $id)->delete();
        return redirect()->route('dashboard.videos')->with('status', 'Canal eliminat correctament.');
    }

    /**
     * Trigger manual video sync from dashboard.
     */
    public function sync(YoutubeVideoService $service)
    {
        $count = $service->syncAll();
        return redirect()->route('dashboard.videos')->with('status', "Sincronització completada! {$count} vídeos processats.");
    }
}

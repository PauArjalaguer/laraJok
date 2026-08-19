<?php

namespace Database\Seeders;

use App\Models\VideoChannel;
use App\Services\YoutubeVideoService;
use Illuminate\Database\Seeder;

class VideoChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            [
                'name' => 'Club Hoquei Prat',
                'type' => 'channel',
                'identifier' => '@ClubHoqueiPrat',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@ClubHoqueiPrat',
                'is_active' => true,
            ],
            [
                'name' => 'Patí Hoquei Club Sant Cugat',
                'type' => 'channel',
                'identifier' => '@patihoqueiclubsantcugat5124',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@patihoqueiclubsantcugat5124',
                'is_active' => true,
            ],
            [
                'name' => 'Copolesa Hoquei',
                'type' => 'channel',
                'identifier' => '@copolesa1862',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@copolesa1862',
                'is_active' => true,
            ],
            [
                'name' => 'Partits Hoquei (Llista)',
                'type' => 'playlist',
                'identifier' => 'PL_wwuYTY1_nFZHUNTGHyD2d20dkAwQU7i',
                'channel_id' => null,
                'playlist_id' => 'PL_wwuYTY1_nFZHUNTGHyD2d20dkAwQU7i',
                'url' => 'https://www.youtube.com/playlist?list=PL_wwuYTY1_nFZHUNTGHyD2d20dkAwQU7i',
                'is_active' => true,
            ],
            [
                'name' => 'CP Voltregà',
                'type' => 'channel',
                'identifier' => '@cpvoltrega',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@cpvoltrega',
                'is_active' => true,
            ],
            [
                'name' => 'Reus Deportiu',
                'type' => 'channel',
                'identifier' => '@ReusDeportiuoficial',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@ReusDeportiuoficial',
                'is_active' => true,
            ],
            [
                'name' => 'Igualada Hoquei Club',
                'type' => 'channel',
                'identifier' => '@igualadahoqueiclub',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@igualadahoqueiclub',
                'is_active' => true,
            ],
            [
                'name' => 'Club Hoquei Lloret',
                'type' => 'channel',
                'identifier' => '@clubhoqueilloret5355',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@clubhoqueilloret5355',
                'is_active' => true,
            ],
            [
                'name' => "Som d'Hoquei",
                'type' => 'channel',
                'identifier' => '@somdhoquei',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@somdhoquei',
                'is_active' => true,
            ],
            [
                'name' => 'HC Sant Just',
                'type' => 'channel',
                'identifier' => '@HCSantJust',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@HCSantJust',
                'is_active' => true,
            ],
            [
                'name' => 'Hoquei Productions',
                'type' => 'channel',
                'identifier' => '@hoqueiproductions',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@hoqueiproductions',
                'is_active' => true,
            ],
            [
                'name' => 'Hoquei Patins ABC',
                'type' => 'channel',
                'identifier' => '@HoqueiPatinsABC',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@HoqueiPatinsABC',
                'is_active' => true,
            ],
            [
                'name' => 'Club Hoquei Santa Perpètua',
                'type' => 'channel',
                'identifier' => '@ClubHoqueiSantaperpetua',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@ClubHoqueiSantaperpetua',
                'is_active' => true,
            ],
            [
                'name' => 'Hoquei Olot TV',
                'type' => 'channel',
                'identifier' => '@HoqueiOlottv',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@HoqueiOlottv',
                'is_active' => true,
            ],
            [
                'name' => 'Club Hoquei Palafrugell',
                'type' => 'channel',
                'identifier' => '@hoqueipalafrugell340',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@hoqueipalafrugell340',
                'is_active' => true,
            ],
            [
                'name' => 'CH Palafrugell Oficial',
                'type' => 'channel',
                'identifier' => '@clubhoqueipalafrugell',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@clubhoqueipalafrugell',
                'is_active' => true,
            ],
            [
                'name' => 'CHP TV',
                'type' => 'channel',
                'identifier' => '@CHP_TV_',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@CHP_TV_',
                'is_active' => true,
            ],
            [
                'name' => 'Girona CH',
                'type' => 'channel',
                'identifier' => '@GIRONACH',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@GIRONACH',
                'is_active' => true,
            ],
            [
                'name' => 'OK Replay Oficial',
                'type' => 'channel',
                'identifier' => '@okreplay_oficial',
                'channel_id' => null,
                'playlist_id' => null,
                'url' => 'https://www.youtube.com/@okreplay_oficial',
                'is_active' => true,
            ],
        ];

        foreach ($channels as $data) {
            VideoChannel::updateOrCreate(
                ['identifier' => $data['identifier']],
                $data
            );
        }
    }
}

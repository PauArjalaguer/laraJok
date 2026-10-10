<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Control de partits obsolets.
 *
 * Els crons copien partits, lligues i grups de la FECAPA i actualitzen la columna
 * `matches.updated` cada vegada que tornen a trobar el partit. Si un partit fa més
 * de N dies que no s'actualitza, és probable que hagi desaparegut de la FECAPA.
 */
class MatchesControlController extends Controller
{
    private const DEFAULT_DAYS = 7;

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->idRole == 1, 403);
    }

    private function staleLimit(int $days): string
    {
        return now()->subDays($days)->format('Y-m-d H:i:s');
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $seasons = DB::table('seasons')->orderByDesc('idSeason')->get();
        $idSeason = (int) $request->input('season', $seasons->first()->idSeason ?? 0);
        $days = max(1, (int) $request->input('days', self::DEFAULT_DAYS));
        $onlyStale = $request->input('stale', '1') === '1';
        $idGroup = $request->input('group');
        $limit = $this->staleLimit($days);

        $staleSql = '(matches.updated IS NULL OR matches.updated < ?)';

        // Resum per grup
        $groups = DB::table('matches')
            ->join('leagues', 'leagues.idLeague', '=', 'matches.idLeague')
            ->leftJoin('phases', 'phases.idGroup', '=', 'matches.idGroup')
            ->leftJoin('categories', 'categories.idCategory', '=', 'leagues.idCategory')
            ->where('leagues.idSeason', $idSeason)
            ->groupBy('matches.idGroup', 'phases.groupName', 'leagues.idLeague', 'leagues.leagueName', 'categories.categoryName')
            ->selectRaw("matches.idGroup, phases.groupName, leagues.idLeague, leagues.leagueName, categories.categoryName,
                COUNT(*) AS total,
                SUM(CASE WHEN $staleSql THEN 1 ELSE 0 END) AS stale,
                MAX(matches.updated) AS lastUpdated", [$limit])
            ->when($onlyStale, fn ($q) => $q->havingRaw('stale > 0'))
            ->orderByDesc('stale')
            ->orderBy('leagues.leagueName')
            ->get();

        // Llistat de partits
        $matches = DB::table('matches')
            ->join('leagues', 'leagues.idLeague', '=', 'matches.idLeague')
            ->leftJoin('phases', 'phases.idGroup', '=', 'matches.idGroup')
            ->leftJoin('categories', 'categories.idCategory', '=', 'leagues.idCategory')
            ->leftJoin('teams as t1', 't1.idTeam', '=', 'matches.idLocal')
            ->leftJoin('teams as t2', 't2.idTeam', '=', 'matches.idVisitor')
            ->where('leagues.idSeason', $idSeason)
            ->when($onlyStale, fn ($q) => $q->whereRaw($staleSql, [$limit]))
            ->when($idGroup, fn ($q) => $q->where('matches.idGroup', $idGroup))
            ->selectRaw("matches.idMatch, matches.idGroup, matches.matchDate, matches.matchHour, matches.idRound,
                matches.localResult, matches.visitorResult, matches.updated,
                t1.teamName AS localTeam, t2.teamName AS visitorTeam,
                phases.groupName, leagues.leagueName, categories.categoryName,
                CASE WHEN $staleSql THEN 1 ELSE 0 END AS isStale", [$limit])
            ->orderByRaw('matches.updated IS NOT NULL, matches.updated ASC')
            ->orderBy('matches.matchDate')
            ->paginate(50)
            ->withQueryString();

        return view('dashboard_matches_control', compact(
            'seasons', 'idSeason', 'days', 'onlyStale', 'idGroup', 'groups', 'matches'
        ));
    }

    /**
     * Elimina un partit (i les seves estadístiques de jugadors).
     */
    public function destroyMatch($idMatch)
    {
        $this->authorizeAdmin();

        $match = DB::table('matches')->where('idMatch', $idMatch)->first();
        abort_unless($match, 404);

        DB::transaction(function () use ($match) {
            $this->deleteMatches([$match->idMatch]);
            DB::table('phases')->where('idGroup', $match->idGroup)->update([
                'numberOfMatches' => DB::table('matches')->where('idGroup', $match->idGroup)->count(),
            ]);
        });

        return back()->with('status', "Partit {$match->idMatch} eliminat.");
    }

    /**
     * Elimina el partit i tot el seu grup: tots els partits del grup, classificacions
     * i la fase. Si la lliga es queda sense grups, també s'elimina.
     */
    public function destroyMatchAndGroup($idMatch)
    {
        $this->authorizeAdmin();

        $match = DB::table('matches')->where('idMatch', $idMatch)->first();
        abort_unless($match, 404);

        return $this->deleteGroup((int) $match->idGroup, (int) $match->idLeague);
    }

    /**
     * Elimina un grup sencer directament des del resum.
     */
    public function destroyGroup($idGroup)
    {
        $this->authorizeAdmin();

        $idLeague = DB::table('phases')->where('idGroup', $idGroup)->value('idLeague')
            ?? DB::table('matches')->where('idGroup', $idGroup)->value('idLeague');
        abort_unless($idLeague, 404);

        return $this->deleteGroup((int) $idGroup, (int) $idLeague);
    }

    private function deleteGroup(int $idGroup, int $idLeague)
    {
        $summary = DB::transaction(function () use ($idGroup, $idLeague) {
            $matchIds = DB::table('matches')->where('idGroup', $idGroup)->pluck('idMatch')->all();
            $this->deleteMatches($matchIds);

            DB::table('classifications')->where('idGroup', $idGroup)->delete();
            DB::table('phases')->where('idGroup', $idGroup)->delete();

            $leagueDeleted = false;
            $hasOtherGroups = DB::table('phases')->where('idLeague', $idLeague)->exists()
                || DB::table('matches')->where('idLeague', $idLeague)->exists();
            if (!$hasOtherGroups) {
                DB::table('classifications')->where('idLeague', $idLeague)->delete();
                DB::table('leagues')->where('idLeague', $idLeague)->delete();
                $leagueDeleted = true;
            }

            return ['matches' => count($matchIds), 'leagueDeleted' => $leagueDeleted];
        });

        Cache::forget('leaguesList');

        $msg = "Grup {$idGroup} eliminat ({$summary['matches']} partits).";
        if ($summary['leagueDeleted']) {
            $msg .= " La lliga {$idLeague} s'ha quedat sense grups i també s'ha eliminat.";
        }

        return redirect()->route('dashboard.matches.control', request()->only('season', 'days', 'stale'))
            ->with('status', $msg);
    }

    private function deleteMatches(array $matchIds): void
    {
        if (empty($matchIds)) {
            return;
        }

        foreach (array_chunk($matchIds, 500) as $chunk) {
            DB::table('player_match')->whereIn('idMatch', $chunk)->delete();
            if (Schema::hasColumn('videos', 'idMatch')) {
                DB::table('videos')->whereIn('idMatch', $chunk)->update(['idMatch' => null]);
            }
            DB::table('matches')->whereIn('idMatch', $chunk)->delete();
        }
    }
}

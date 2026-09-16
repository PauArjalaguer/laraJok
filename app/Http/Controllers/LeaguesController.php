<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Leagues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaguesController extends Controller
{
    /**
     * Admin dashboard view for managing league categories.
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        $seasonId = $request->input('season');
        $categoryId = $request->input('category');

        $query = DB::table('leagues')
            ->leftJoin('seasons', 'leagues.idSeason', '=', 'seasons.idSeason')
            ->leftJoin('categories', 'leagues.idCategory', '=', 'categories.idCategory')
            ->select(
                'leagues.idLeague',
                'leagues.leagueName',
                'leagues.idSeason',
                'leagues.idCategory',
                'seasons.seasonName',
                'categories.categoryName'
            );

        if (!empty($seasonId)) {
            $query->where('leagues.idSeason', $seasonId);
        }

        if ($categoryId !== null && $categoryId !== '') {
            $query->where('leagues.idCategory', $categoryId);
        }

        if (!empty($search)) {
            $query->where('leagues.leagueName', 'like', '%' . $search . '%');
        }

        // Order by season desc, category asc as requested (0 stays at top)
        $leagues = $query->orderBy('leagues.idSeason', 'desc')
            ->orderBy('leagues.idCategory', 'asc')
            ->orderBy('leagues.leagueName', 'asc')
            ->paginate(50)
            ->withQueryString();

        $categories = Categories::orderBy('categoryName', 'asc')->get();
        $seasons = DB::table('seasons')->orderBy('idSeason', 'desc')->get();

        return view('dashboard_leagues', compact('leagues', 'categories', 'seasons', 'search', 'seasonId', 'categoryId'));
    }

    /**
     * Update the category assigned to a specific league.
     */
    public function updateCategory(Request $request)
    {
        $validated = $request->validate([
            'idLeague' => 'required|integer',
            'idCategory' => 'required|integer',
        ]);

        $idLeague = $validated['idLeague'];
        $idCategory = $validated['idCategory'];

        DB::table('leagues')
            ->where('idLeague', $idLeague)
            ->update(['idCategory' => $idCategory]);

        // Invalidate database and application cache
        Cache::flush();
        Artisan::call('cache:clear');

        $categoryName = null;
        if ($idCategory > 0) {
            $cat = Categories::find($idCategory);
            $categoryName = $cat ? $cat->categoryName : null;
        }

        return response()->json([
            'success' => true,
            'idLeague' => $idLeague,
            'idCategory' => (int) $idCategory,
            'categoryName' => $categoryName,
            'message' => 'Categoria actualitzada correctament.',
        ]);
    }

    /**
     * Create a new category and optionally assign it to a league.
     */
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'categoryName' => 'required|string|max:50',
            'idLeague' => 'nullable|integer',
        ]);

        $categoryName = trim($validated['categoryName']);

        // Check if category already exists (case-insensitive)
        $category = Categories::whereRaw('LOWER(categoryName) = ?', [mb_strtolower($categoryName)])->first();

        if (!$category) {
            $category = Categories::create([
                'categoryName' => $categoryName,
            ]);
        }

        $idLeague = $validated['idLeague'] ?? null;
        if ($idLeague) {
            DB::table('leagues')
                ->where('idLeague', $idLeague)
                ->update(['idCategory' => $category->idCategory]);
        }

        // Invalidate database and application cache
        Cache::flush();
        Artisan::call('cache:clear');

        $allCategories = Categories::orderBy('categoryName', 'asc')->get();

        return response()->json([
            'success' => true,
            'category' => $category,
            'idLeague' => $idLeague,
            'allCategories' => $allCategories,
            'message' => 'Categoria creada i assignada correctament.',
        ]);
    }
}

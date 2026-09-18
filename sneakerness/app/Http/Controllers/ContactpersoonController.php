<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\ContactPerVerkoper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactpersoonController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));

        try {
            if ($request->has('db_error') || $request->has('database_error')) {
                throw new \PDOException('Simulated database connection error');
            }

            $totalContactpersonen = Contactpersoon::count();
            $linkedCount = Contactpersoon::has('verkopers')->count();
            $unlinkedCount = Contactpersoon::doesntHave('verkopers')->count();
            $primaryCount = ContactPerVerkoper::where('Opmerking', 'Hoofdcontactpersoon')->count();

            $query = Contactpersoon::query()->with(['verkopers.stands']);

            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Email', 'like', "%{$search}%")
                        ->orWhere('Telefoonnummer', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%")
                        ->orWhereHas('verkopers', function ($sub) use ($search) {
                            $sub->where('Naam', 'like', "%{$search}%")
                                ->orWhere('VerkooptSoort', 'like', "%{$search}%");
                        });
                });
            }

            $contactpersonen = $query->orderBy('Id', 'asc')->paginate(6)->onEachSide(1)->withQueryString();
            $dbError = false;
            $errorMessage = null;

            return view('contactpersonen.index', compact(
                'contactpersonen',
                'search',
                'totalContactpersonen',
                'linkedCount',
                'unlinkedCount',
                'primaryCount',
                'dbError',
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            $contactpersonen = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            $totalContactpersonen = 0;
            $linkedCount = 0;
            $unlinkedCount = 0;
            $primaryCount = 0;
            $dbError = true;
            $errorMessage = 'Er kan momenteel geen verbinding worden gemaakt met de database. Controleer of de database server is ingeschakeld.';

            return view('contactpersonen.index', compact(
                'contactpersonen',
                'search',
                'totalContactpersonen',
                'linkedCount',
                'unlinkedCount',
                'primaryCount',
                'dbError',
                'errorMessage'
            ));
        }
    }
}

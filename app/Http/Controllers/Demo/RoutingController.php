<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    /**
     * Segmentos permitidos para evitar path traversal.
     */
    private function sanitize(string $segment): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '', $segment);
    }

    public function index(Request $request)
    {
        return view('template.dashboards.index');
    }

    public function root(Request $request, $first)
    {
        $view = 'template.' . $this->sanitize($first);
        if (!view()->exists($view))
            abort(404);
        return view($view);
    }

    public function secondLevel(Request $request, $first, $second)
    {
        $view = 'template.' . $this->sanitize($first) . '.' . $this->sanitize($second);
        if (!view()->exists($view))
            abort(404);
        return view($view);
    }

    public function thirdLevel(Request $request, $first, $second, $third)
    {
        $view = 'template.' . $this->sanitize($first) . '.' . $this->sanitize($second) . '.' . $this->sanitize($third);
        if (!view()->exists($view))
            abort(404);
        return view($view);
    }
}

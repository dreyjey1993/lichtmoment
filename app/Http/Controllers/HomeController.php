<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $portfolioPhotos = Photo::whereNull('project_id')
            ->orWhere('project_id', 0)
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        $photographer = [
            'name' => 'Markus Knuth',
            'tagline' => 'Hochzeitsfotografie mit Seele',
            'bio' => [
                'Ich bin Markus Knuth – Hochzeitsfotograf aus Leidenschaft seit über 10 Jahren.',
                'Für mich ist jede Hochzeit eine einzigartige Liebesgeschichte, die es verdient, in Bildern verewigt zu werden. Ich arbeite diskret, einfühlsam und mit einem Blick für die kleinen Momente, die am Ende die größten Erinnerungen werden.',
                'Von der freien Trauung im Wald bis zur großen Feier im Schloss – ich begleite euch mit meiner Kamera und sorge dafür, dass ihr euren Tag in vollen Zügen genießen könnt, während ich die Emotionen einfange.',
            ],
            'phone' => '+49 171 234 56 78',
            'email' => 'info@lichtmoment.de',
        ];

        return view('home', compact('portfolioPhotos', 'photographer'));
    }

    public function impressum()
    {
        return view('impressum');
    }

    public function datenschutz()
    {
        return view('datenschutz');
    }
}

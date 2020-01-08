<?php


namespace App\Controllers;


use App\Doctag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocTagsController extends Controller
{
    public function doctags(Request $request)
    {
        $data = $request->query->all();

        $limit = $data['limit'] ?? 10;
        $tag = isset($data['tag']) ? trim($data['tag']) : null;


        // Preparing query
        $doctags = Doctag::select('*')->limit($limit);
        if (isset($tag)) $doctags->where('Tag', 'LIKE', "%$tag%");

        // Getting doctags
        $doctags = $doctags->getAll();

        // Preparing JSON
        $doctags = json_encode($doctags);

        // Preparing response
        $response = new Response(
            $doctags,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
        $response->prepare($request);

        // Sending response
        $response->send();
    }
}
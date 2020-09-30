<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function showAllArticle()
    {
        $result = response()->json(Article::all(), 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
        return $result;
    }

    public function showOneArticle($id)
    {
        $result = response()->json(Article::find($id), 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
        return $result;
    }

    public function create(Request $request)
    {
        $this->validate($request, array(
            'title' => 'required',
            'description' => 'required'
        ));

        $article = Article::create($request->all());

        return response()->json($article, 201,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    public function update($id, Request $request)
    {
        $this->validate($request, array(
            'title' => 'required',
            'description' => 'required'
        ));

        $article = Article::findOrFail($id);
        $article->update($request->all());

        return response()->json($article, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    public function delete($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response('Deleted successfully', 200);
    }
}

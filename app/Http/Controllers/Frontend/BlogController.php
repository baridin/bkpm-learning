<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Blog;
use App\Category;
use TCG\Voyager\Traits\Resizable;

class BlogController extends Controller
{
    
    public function index(Request $request, Blog $Blog){
    	// $blogs = Blog::paginate(10);
    	

    	$newsListQuery = Blog::where('status', '=', 'PUBLISHED');

    	if ($request->has('query')) {
            $newsListQuery->where('title', 'like', '%' . $request->query('query') . '%');
        }

        $filter = '';
        if ($request->has('filter')) {
            $filter = $request->query('filter');
            if ($filter == 'oldest') {
                $newsListQuery->orderBy('created_at', 'ASC');
            } elseif ($filter == 'asc') {
                $newsListQuery->orderBy('title', 'ASC');
            } elseif ($filter == 'desc') {
                $newsListQuery->orderBy('title', 'DESC');
            } else {
                $newsListQuery->orderBy('created_at', 'DESC');
            }
        } else {
            $filter = 'ptt';
            $newsListQuery->orderBy('created_at', 'DESC');
        }

        $newsList = $newsListQuery->paginate(9);
        $filters = [
            [
                'key' => 'asc',
                'value' => 'Judul A to Z',
            ],
            [
                'key' => 'desc',
                'value' => 'Judul Z to A',
            ],
            [
                'key' => 'newest',
                'value' => 'Terbaru',
            ],
            [
                'key' => 'oldest',
                'value' => 'Terlama',
            ]
        ];

       

        return view('frontend.blog.index', [
            'title' => 'PTP',
            // 'popularNews' => $popularNews,
            'newsList' => $newsList,
            'news' => $Blog,
            'filter' => $filter,
            'filters' => $filters,
            
        ]);
    	// return view('frontend.blog.index',compact(array('kategoris','ptt','newsList')));
    }

    public function detail($slug){
    	
    	$blog_list = Blog::where('status', '=', 'PUBLISHED')->get();
    	$blog = Blog::where('status', '=', 'PUBLISHED')->where('slug', '=', $slug)->first();
    	
    	$youtube = $this->convertYoutube($blog->youtube);
    	if (!$blog) {
    		abort(404);
    	}
    	return view('frontend.blog.detail',compact(array('blog','blog_list','youtube')));

    }

    function convertYoutube($string) {
    return preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "$2",
        $string
    );
	}
    public function link(){
        return redirect()->to(url('admin/survey-feedback-instruktursss'));
    }
    
    public function unitkerja(){
        return redirect()->to(url('admin/unit-kerja'));
    }
    
    
}

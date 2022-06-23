<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Book::select('id','title','description','image')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'image'=>'required|image' ]);
            try{
                $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('book/image', $request->image,$imageName);
                Book::create($request->post()+['image'=>$imageName]);
    
                return response()->json([
                    'message'=>'Book Created Successfully!!'
                ]);
            }catch(\Exception $e){
                \Log::error($e->getMessage());
                return response()->json([
                    'message'=>'Something goes wrong while creating a book!!'
                ],500);
            } 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return response()->json([
            'book'=>$book
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'image'=>'nullable'
        ]);

        try{

            $book->fill($request->post())->update();

            if($request->hasFile('image')){

                // remove old image
                if($book->image){
                    $exists = Storage::disk('public')->exists("book/image/{$book->image}");
                    if($exists){
                        Storage::disk('public')->delete("book/image/{$book->image}");
                    }
                }

                $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('book/image', $request->image,$imageName);
                $book->image = $imageName;
                $book->save();
            }

            return response()->json([
                'message'=>'Book Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a book!!'
            ],500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        try {

            if($book->image){
                $exists = Storage::disk('public')->exists("book/image/{$book->image}");
                if($exists){
                    Storage::disk('public')->delete("book/image/{$book->image}");
                }
            }

            $book->delete();

            return response()->json([
                'message'=>'Book Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a book!!'
            ]);
        }
    
    

    }
}
?>
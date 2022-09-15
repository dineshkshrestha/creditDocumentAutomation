<?php

namespace App\Http\Controllers;

use App\Http\Requests\PdfMergeCreateRequest;
use Illuminate\Http\Request;

class PdfMergeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
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
        return view('pdfmerge.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PdfMergeCreateRequest $request)
    {
        $file = $request->file('file');

        foreach ($file as $f) {
            $filename = $f->store('photos');

        }
        dd('dd');
        if (!empty($request->file('file'))) {
            $file = $request->file('file');

            $pdf = new \Jurosh\PDFMerge\PDFMerger();

// add as many pdfs as you want


            foreach ($file as $f) {
                $f->getRealPath();
                echo $f;
            }


            dd('he');

            $path1 = storage_path('pdf/1.pdf');
            $path2 = storage_path('pdf/2.pdf');


            $pdf->addPDF($path1, 'all')
                ->addPDF($path2, 'all');

// call merge, output format `file`
            $path = storage_path('pdf/final.pdf');
            $pdf->merge('file', $path);


            $path = storage_path() . '/pdf/';
            $pdf = $request->file('thumb_image');
            $name = $pdf->getClientOriginalName();
            if ($pdf->move($path, $name)) {
                $product['thumb_image'] = $name;
            }

        }


        dd($request);

        $pdf = new \Jurosh\PDFMerge\PDFMerger();

// add as many pdfs as you want

        $path1 = storage_path('pdf/1.pdf');
        $path2 = storage_path('pdf/2.pdf');


        $pdf->addPDF($path1, 'all')
            ->addPDF($path2, 'all');

// call merge, output format `file`
        $path = storage_path('pdf/final.pdf');
        $pdf->merge('file', $path);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

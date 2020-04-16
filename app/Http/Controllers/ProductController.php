<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\ProductIndexQuery;
use App\Http\Requests\ProductUpdateQuery;
use App\Http\Services\CommonService;
use App\Mail\ProductReport;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    private $service;

    public function __construct(CommonService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param ProductIndexQuery $request
     * @return \Illuminate\Http\Response
     */
    public function index(ProductIndexQuery $request)
    {
        $options = config('options');
        $query = $this->service->indexQuery($request);
        if($request->has('common')){
            $chart = $this->service->chart($query->get(), false);
        }else{
            $chart = $this->service->chart($query->get());
        }

        $products = $query->paginate(15)->appends(request()->query());
        $request->flash();

        return response()->view('welcome', compact(['products', 'options', 'chart']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $clients = Client::all(['id', 'name']);
        return view('edit', compact(['product', 'clients']));
    }

    /**
     * @param ProductUpdateQuery $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductUpdateQuery $request, Product $product)
    {
        $product->fill($request->validated());
        if ($product->save()) {
            Session::flash('message', 'Successfully updated the product!');
            return redirect()->route('product.index');
        } else {
            Session::flash('error', 'The product was not updated');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            Session::flash('message', 'Successfully deleted the product!');
            return redirect()->route('product.index');
        } else {
            Session::flash('error', 'The product was not deleted');
            return redirect()->back();
        };
    }

    /**
     * @param ProductIndexQuery $request
     * @return string
     * @throws \ReflectionException
     */
    public function ship(ProductIndexQuery $request)
    {
        $request = $this->service->indexQuery($request);

        Mail::to(User::find(1))
            ->queue(new ProductReport($request->get()));
        //        return  (new ProductReport($request->get()))->render();
        Session::flash('message', 'Successfully send product!');
        return redirect()->back();
    }
}

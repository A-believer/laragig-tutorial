<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // show all listing
    public function index() {
return view('listings.index', [
        'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(3)
    ]);
    }

     // show single listing
    public function show(Listing $listing) {
        return view('listings.show', 
['listing' => $listing]);
    }

    //Show Create Form
    public function create() {
        return view('listings.create');
    }

    //Store listing data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        //file upload validation
        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $formFields['user_id'] = auth()->id();
        Listing::create($formFields);


        //redirect user to home page after creating listing
        return redirect('/')->with('message', 'Listing created successfully!');
    }


    // Show Edit Form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }



    //Store listing data
    public function update(Request $request, Listing $listing) {
        // make sure user is authorized to update
        if($listing->user_id != auth()->id()) {
            abort(403, 'unauthorized action');
        }
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        //file upload validation
        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $listing->update($formFields);

        //redirect user to home page after creating listing
        return redirect('/listings/' . $listing->id)->with('message', 'Listing updated successfully!');
    }

//Delete Listing
    public function destroy(Listing $listing) {
        // make sure user is authorized to delete
        if($listing->user_id != auth()->id()) {
            abort(403, 'unauthorized action');
        }
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully!');
    }

    // Manage Listing
    public function manage() {
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}

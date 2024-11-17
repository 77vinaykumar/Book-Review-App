<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //This method will show review in backend
    public function index(Request $request)
    {
        $reviews = Review::with('book', 'user')->orderBy('created_at', 'DESC');

        if (!empty($request->keyword)) {
            $reviews = $reviews->where('review', 'like', '%' . $request->keyword . '%');
        }
        $reviews = $reviews->paginate(5);
        return view('account.reviews.list', [
            'reviews' => $reviews
        ]);
    }

    //This method will show edit review page
    public function edit($id)
    {
        $review = Review::findorFail($id);
        return view('account.reviews.edit', [
            'review' => $review
        ]);
    }

    //This method will update a review
    public function updateReview($id, Request $request)
    {
        $review = Review::findorFail($id);

        $validator = Validator::make($request->all(), [
            'review' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.reviews.edit', $id)->withInput()->withErrors($validator);
        }

        $review->review = $request->input('review');
        $review->status = $request->input('status');
        $review->save();


        session()->flash('success', 'Review updated successfully.');
        return redirect()->route('account.reviews');
    }


    //This method will delete a review from database
    public function deleteReview(Request $request)
    {
        $id = $request->id;

        $review = Review::find($id);

        if ($review == null) {
            session()->flash('error', 'Review not found');
            return response()->json([
                'status' => false
            ]);
        } else {
            $review->delete();

            session()->flash('success', 'Review deleted successfully');
            return response()->json([
                'status' => false
            ]);
        }
    }
}

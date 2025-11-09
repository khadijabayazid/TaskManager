<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function store(StoreProfileRequest $request)
    {
        $userId = Auth::user()->id;
        $validated = $request->validated();
        $validated['user_id'] = $userId;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('my photo', 'public');
            $validated['image'] = $path;
        }
        $profile = Profile::create($validated);
        return response()->json([
            'message' => 'Profile created successfully',
            'profile' => $profile
        ], 201);
    }

    public function show()
    {
        $profile = Profile::where('user_id', Auth::user()->id)->firstOrFail();
        return response()->json($profile, 200);
    }

    public function update(UpdateProfileRequest $request,)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();
        $profile->update($request->validated());
        return response()->json($profile, 200);
    }
}

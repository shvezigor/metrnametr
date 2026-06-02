<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $statusCode = 200;
            $image = Image::findOrFail($id);
            $fileName = basename($image->location);
            $response = $image->delete();
            Storage::disk('products')->delete($fileName);

        } catch (Exception $e) {

            $statusCode = 400;
            $response = $e->getMessage();

        } finally {
            return response()->json($response, $statusCode);
        }
    }


}

<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends ResponseController
{
    use Common;

    public function keywordSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $keyword = $request->keyword;
        if(!empty($keyword))
        {
            $result = $this->getTrendingProducts(1, ['keyword' => $request->keyword, 'paginate' => 20]);
        }
        else{
            $result = $this->getTrendingProducts(1, ['paginate' => 20]);
        }

        $message = "Showing search results for ".$request->keyword;
        return $this->sendResponse(1, $message, $result);
    }
}
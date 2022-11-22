<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Users\User;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $user;

    public function user()
    {

        if (! $this->user) $this->user = User::find(Auth::id());

        return $this->user;
    }


    public function authorise(array $conditions)
    {

        $authorised = true;

        foreach ($conditions as $condition) {

            if (! (bool)$condition) {
                $authorised = false;
                break;
            }
        }

        return $authorised ? true : abort(Response::HTTP_UNAUTHORIZED);

    }


    public function getOrderParams(Request $request) : array
    {

        $orderKey = $request->get('order_key') ? $request->get('order_key') : 'created_at';
        $orderDirection = $request->get('order_direction') ? $request->get('order_direction') : 'desc';

        return [
            'order_key' => $orderKey,
            'order_direction' => $orderDirection,
        ];

    }


    public function getFilterParams(Request $request, array $accepted = []) : array
    {

        $arr = [];

        foreach ($accepted as $key) {

            $value = $request->get($key);

            if ($value !== null && $value !== 'null') $arr[$key] = $value;
        }

        return $arr;

    }


    public function getPaginationParams(Request $request) : array
    {

        $page = $request->get('page') ? $request->get('page') : 1;
        $perPage = $request->get('per_page') ? $request->get('per_page') : 20;

        return [
            'page' => $page,
            'per_page' => $perPage,
        ];

    }


    public function flashMessage(string $type, $msg)
    {

        return [
            'type' => $type,
            'msg' => $msg,
        ];

    }


}

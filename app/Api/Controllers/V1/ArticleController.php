<?php

/**
 * 用户控制器
 */
namespace App\Api\Controllers\V1;

use App\Api\Controllers\BaseController;
use App\Api\Transformers\ArticleTransformer;
use App\Article;

class ArticleController extends BaseController
{

    /**
     * @api {get} /users 用户列表
     * @apiDescription 当前用户信息
     * @apiGroup user
     * @apiPermission none
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *           "id": 2,
     *           "email": "490554191@qq.com",
     *           "name": "fff",
     *           "created_at": "2015-11-12 10:37:14",
     *           "updated_at": "2015-11-13 02:26:36",
     *           "deleted_at": null
     *         }
     *       ],
     *       "meta": {
     *         "pagination": {
     *           "total": 1,
     *           "count": 1,
     *           "per_page": 15,
     *           "current_page": 1,
     *           "total_pages": 1,
     *           "links": []
     *         }
     *       }
     *     }
     */
    public function index()
    {
        return $this->response->array(Article::all(), new ArticleTransformer);
        $article = Article::paginate();

        return $this->response->paginator($article, new ArticleTransformer);
    }
    /**
     * @api {get} /user 某个用户信息
     * @apiDescription 某个用户信息
     * @apiGroup user
     * @apiPermission none
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": {
     *         "id": 2,
     *         "email": "490554191@qq.com",
     *         "name": "fff",
     *         "created_at": "2015-11-12 10:37:14",
     *         "updated_at": "2015-11-13 02:26:36",
     *         "deleted_at": null
     *       }
     *     }
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($user, new UserTransformer);
    }
}
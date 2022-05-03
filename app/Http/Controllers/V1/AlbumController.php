<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Http\Resources\V1\AlbumResource;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
     /**
     * Get List of Albums
     * @OA\Get (
     *     path="{{BASE_URL}}/api/v1/album",
     *     tags={"Albums"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\data(
     *                     type="array of Objects",
     *                     @OA\Property(
     *                         property="_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Computers"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        return AlbumResource::collection(Album::where('user_id',$request->user()->id)->paginate());
    }

    /**
     * Create Albums
     * @OA\Post (
     *     path="{{BASE_URL}}/api/v1/album/",
     *     tags={"Album"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *
     *                 ),
     *                 example={
     *                     "name":"Computer",
     *
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Computers"),
     *             @OA\Property(property="album_id", type="number", example="3"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      )
     * )
     */
    public function store(StoreAlbumRequest $request)
    {
        $data = $request->all();
        $data['user_id']=$request->user()->id;
        $album =Album::create($data);
        return new AlbumResource($album);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,Album $album)
    {
        if($request->user()->id != $album->user_id){
            return abort(403,'Unauthorised');
        }
        return new AlbumResource($album);
    }

    /**
     * Update Album
     * @OA\Put (
     *     path="{{BASE_URL}}/api/v1/album/1",
     *     tags={"Album"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Computer updated",
     *
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Computer updated"),
     *
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(UpdateAlbumRequest $request, Album $album)
    {
        if($request->user()->id != $album->user_id){
            return abort(403,'Unauthorised');
        }
        $album->update($request->all());
        return new AlbumResource($album);
    }

     /**
     * Delete Todo
     * @OA\Delete (
     *     path="{{BASE_URL}}/api/v1/album/1",
     *     tags={"Album"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete Album success")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request,Album $album)
    {
        if($request->user()->id != $album->user_id){
            return abort(403,'Unauthorised');
        }
        $album->delete();
        return response('delete Album success',204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResourceCollection;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return new JsonResponse([
            'message' => 'User successfully created',
            'data' => $user
        ], 201);
    }

    /**
     * @param User $user
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function update(User $user, UserRequest $request)
    {
        $user = $this->userService->update($user, $request->validated());

        return new JsonResponse([
            'message' => 'User successfully updated',
            'data' => $user
        ], 201);

    }

    /**
     * User list: endpoint for user list, returns all the information about all existing users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::paginate();
        return (new UserResourceCollection($users))->response();
    }

    /**
     * User delete: Endpoint for user deletion. Remove user and it's details, if exists.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return new JsonResponse([
            'message' => "User deleted successfully.",
        ], 204);
    }
}

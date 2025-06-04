<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\HandlesApiResponses;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\UnauthorizedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Exceptions\RequestTimeoutException;
use App\Exceptions\ResourceCreatedException;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\RefreshTokenExpiredException;
use App\Exceptions\TokenExpiredException as CustomTokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as TymonTokenExpiredException;
use App\Traits\ChecksRequestTimeout;



class BlogController extends Controller
{
    use HandlesApiResponses, ChecksRequestTimeout;

    public function getAllOrOneOrDestroy(Request $request, $id = null)
    {
        // // Example: Unauthorized access
        // if ($request->header('X-Unauthorized') === '1') {
        //     throw new UnauthorizedException('You must be logged in.');
        // }

        // // Example: Forbidden access
        // if ($request->header('X-Forbidden') === '1') {
        //     throw new ForbiddenException('You do not have permission to access this resource.');
        // }

        // // Example: Invalid token
        // if ($request->header('X-Invalid-Token') === '1') {
        //     throw new InvalidTokenException('access_token', 'invalid');
        // }

        // // Example: Token expired
        // if ($request->header('X-Token-Expired') === '1') {
        //     throw new TokenExpiredException('access_token');
        // }

        // // Example: Refresh token expired
        // if ($request->header('X-Refresh-Token-Expired') === '1') {
        //     throw new RefreshTokenExpiredException();
        // }

        // // Example: Request timeout
        // if ($request->header('X-Request-Timeout') === '1') {
        //     throw new RequestTimeoutException();
        // }

        $start = microtime(true);
        try {
            $user = Auth::user();
            if (!$user) {
                throw new UnauthorizedException('You must be logged in.');
            }
            if (!$user->isAdmin()) {
                throw new ForbiddenException('You do not have permission to view or delete blogs.');
            }

            try {
                $user = JWTAuth::parseToken()->authenticate();
                if (!$user) {
                    throw new InvalidTokenException('access token', 'invalid user');
                }
            } catch (TymonTokenExpiredException $e) {
                throw new CustomTokenExpiredException();
            } catch (JWTException $e) {
                throw new InvalidTokenException('access token', 'invalid');
            }

            // sleep(2); // Simulate a slow operation for demo purposes
            // Check for timeout before proceeding
            $this->checkRequestTimeout($start, 1); // 1 second for demo
        } catch (NotFoundException $e) {
            throw $e;
        } catch (RequestTimeoutException $e) {
            throw $e; // Let the handler return 408
        } catch (\Throwable $e) {
            Log::error('Error in BlogController@getAllOrOneOrDestroy: ' . $e->getMessage());
            throw new InternalServerErrorException('Failed to fetch blogs', ['error' => $e->getMessage()]);
        }
        if (!in_array($request->method(), ['GET', 'DELETE'])) {
            throw new \App\Exceptions\MethodNotAllowedException();
        }
        if ($request->isMethod('delete')) {
            $blog = Blog::find($id);
            if (!$blog) {
                throw new NotFoundException('Blog', $id);
            }
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $blog->delete();
            return $this->successResponse(null, 'Blog deleted');
        }
        if ($id) {
            $blog = Blog::find($id);
            if (!$blog) {
                throw new NotFoundException('Blog', $id);
            }
            return $this->successResponse($blog, 'Blog found');
        }
        $blogs = Blog::all();
        return $this->successResponse($blogs, 'Blog list fetched');
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $start = microtime(true);

        try {
            $user = Auth::user();
            if (!$user) {
                throw new UnauthorizedException('You must be logged in.');
            }
            if (!$user->isAdmin()) {
                throw new ForbiddenException('You do not have permission to create or update blogs.');
            }

            try {
                $user = JWTAuth::parseToken()->authenticate();
                if (!$user) {
                    throw new InvalidTokenException('access token', 'invalid user');
                }
            } catch (TymonTokenExpiredException $e) {
                throw new CustomTokenExpiredException();
            } catch (JWTException $e) {
                throw new InvalidTokenException('access token', 'invalid');
            }

            if (!in_array($request->method(), ['POST', 'PUT'])) {
                throw new \App\Exceptions\MethodNotAllowedException();
            }

            $errors = [];
            $data = $request->has('data')
                ? json_decode($request->input('data'), true)
                : ($request->isJson() ? $request->json()->all() : $request->only(['title', 'content']));

            if (!isset($data['title']) || empty($data['title'])) {
                $errors['title'][] = 'Title is required.';
            }
            if (!isset($data['content']) || empty($data['content'])) {
                $errors['content'][] = 'Content is required.';
            }
            if ($errors) {
                throw new ValidationException($errors);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('blogs', 'public');
                $data['image'] = $image;
            }

            if ($id) {
                $blog = Blog::find($id);
                if (!$blog) {
                    throw new NotFoundException('Blog', $id);
                }
                if ($request->hasFile('image') && $blog->image) {
                    Storage::disk('public')->delete($blog->image);
                }
                $blog->update($data);
                if ($request->header('X-Resource-Created') === '1') {
                    throw new ResourceCreatedException('Resource created successfully (demo)');
                }
                // Check for timeout before returning
                sleep(2); // Sleep for 2 seconds to simulate a slow operation
                $this->checkRequestTimeout($start, 1); // 1 second for demo
                return $this->successResponse($blog, 'Blog updated');
            } else {
                $blog = Blog::create($data);
                if ($request->header('X-Resource-Created') === '1') {
                    throw new ResourceCreatedException();
                }
                // Check for timeout before returning
                sleep(2); // Sleep for 2 seconds to simulate a slow operation
                $this->checkRequestTimeout($start, 1); // 1 second for demo
                return $this->successResponse($blog, 'Blog created', 201);
            }
        } catch (NotFoundException $e) {
            throw $e;
        } catch (RequestTimeoutException $e) {
            throw $e; // Let the handler return 408
        } catch (\Throwable $e) {
            throw new InternalServerErrorException('Failed to save blog', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Dummy token validation. Replace with real logic as needed.
     */
    protected function isTokenInvalid($token): bool
    {
        // Implement your real token validation here
        // For now, always return false (token is valid)
        return false;
    }

    /**
     * Dummy token expiration check. Replace with real logic as needed.
     */
    protected function isTokenExpired($token): bool
    {
        // Implement your real token expiration logic here
        // For now, always return false (token is not expired)
        return false;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Traits\HandlesApiResponses;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\RequestTimeoutException;
use App\Exceptions\ResourceCreatedException;
use App\Traits\AuthenticatedAdminCheck;
use App\Traits\ChecksRequestTimeout;

class BlogController extends Controller
{
    use HandlesApiResponses, ChecksRequestTimeout, AuthenticatedAdminCheck;

    public function getAllOrOneOrDestroy(Request $request, $id = null)
    {
        $start = microtime(true);
        try {
            $this->checkIfAuthenticatedAndAdmin();
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $start = microtime(true);

        try {
            $this->checkIfAuthenticatedAndAdmin();
            // Check HTTP method first!
            if (!in_array($request->method(), ['POST'])) {
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
                $data['image'] = url("storage/{$image}");
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
                // sleep(2); // Sleep for 2 seconds to simulate a slow operation
                $this->checkRequestTimeout($start, 1); // 1 second for demo
                return $this->successResponse($blog, 'Blog updated');
            } else {
                $blog = Blog::create($data);
                if ($request->header('X-Resource-Created') === '1') {
                    throw new ResourceCreatedException();
                }
                // Check for timeout before returning
                // sleep(2); // Sleep for 2 seconds to simulate a slow operation
                $this->checkRequestTimeout($start, 1); // 1 second for demo
                return $this->successResponse($blog, 'Blog created', 201);
            }
        } catch (NotFoundException $e) {
            throw $e;
        } catch (RequestTimeoutException $e) {
            throw $e;
        }
    }


    public function validateToken(Request $request)
    {
        $token = $request->input('token');

        if ($token !== 'valid-token') {
            throw new InvalidTokenException('access_token', 'invalid');
        }

        return $this->successResponse(null, 'Token is valid');
    }
}

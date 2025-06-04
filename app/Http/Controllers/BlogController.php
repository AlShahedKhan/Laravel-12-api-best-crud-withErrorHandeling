<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\HandlesApiResponses;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Exceptions\InternalServerErrorException;

class BlogController extends Controller
{
    use HandlesApiResponses;

    // GET /blogs or /blogs/{id} or DELETE /blogs/{id}
    public function getAllOrOneOrDestroy(Request $request, $id = null)
    {
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

    // POST or PUT /blogs or /blogs/{id}
    public function storeOrUpdate(Request $request, $id = null)
    {
        if (!in_array($request->method(), ['POST'])) {
            throw new \App\Exceptions\MethodNotAllowedException();
        }
        $errors = [];
        // Parse JSON from 'data' key if present
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('blogs', 'public');
            $data['image'] = $image;
        }

        try {
            if ($id) {
                $blog = Blog::find($id);
                if (!$blog) {
                    // Throw NotFoundException directly, not inside try-catch
                    throw new NotFoundException('Blog', $id);
                }
                // Delete old image if new one uploaded
                if ($request->hasFile('image') && $blog->image) {
                    Storage::disk('public')->delete($blog->image);
                }
                $blog->update($data);
                return $this->successResponse($blog, 'Blog updated');
            } else {
                $blog = Blog::create($data);
                return $this->successResponse($blog, 'Blog created', 201);
            }
        } catch (NotFoundException $e) {
            // Rethrow NotFoundException so it is not caught as InternalServerError
            throw $e;
        } catch (\Throwable $e) {
            throw new InternalServerErrorException('Failed to save blog', ['error' => $e->getMessage()]);
        }
    }
}

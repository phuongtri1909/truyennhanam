<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    private const RESTRICTED_FOR_ADMIN_SUB = 'admin_sub_can_approve_stories';

    /**
     * Display a listing of the configurations.
     */
    public function index()
    {
        $configs = Config::orderBy('key')->get();

        if (Auth::user()->role === 'admin_sub') {
            $configs = $configs->reject(fn ($c) => $c->key === self::RESTRICTED_FOR_ADMIN_SUB);
        }

        return view('admin.pages.configs.index', compact('configs'));
    }

    /**
     * Show the form for creating a new configuration.
     */
    public function create()
    {
        return view('admin.pages.configs.create');
    }

    /**
     * Store a newly created configuration in storage.
     */
    public function store(Request $request)
    {
        if ($request->key === self::RESTRICTED_FOR_ADMIN_SUB && Auth::user()->role === 'admin_sub') {
            abort(403, 'Chỉ admin chính mới được tạo cấu hình này.');
        }

        $request->validate([
            'key' => 'required|string|max:255|unique:configs',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Config::setConfig($request->key, $request->value, $request->description);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified configuration.
     */
    public function edit(Config $config)
    {
        if ($config->key === self::RESTRICTED_FOR_ADMIN_SUB && Auth::user()->role === 'admin_sub') {
            abort(403, 'Chỉ admin chính mới được chỉnh cấu hình này.');
        }
        return view('admin.pages.configs.edit', compact('config'));
    }

    /**
     * Update the specified configuration in storage.
     */
    public function update(Request $request, Config $config)
    {
        if ($config->key === self::RESTRICTED_FOR_ADMIN_SUB && Auth::user()->role === 'admin_sub') {
            abort(403, 'Chỉ admin chính mới được chỉnh cấu hình này.');
        }

        $request->validate([
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Config::setConfig($config->key, $request->value, $request->description);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được cập nhật thành công.');
    }

} 
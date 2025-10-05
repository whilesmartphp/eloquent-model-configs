<?php

namespace Whilesmart\ModelConfiguration\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Whilesmart\ModelConfiguration\Documentation\IConfigurationControllerInterface;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Traits\ApiResponse;

class ConfigurationController extends Controller implements IConfigurationControllerInterface
{
    use ApiResponse;

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',
            'type' => 'required|in:string,int,float,bool,array,json,date',
        ]);

        if ($validator->fails()) {
            return $this->failure('Validation failed.', 422, [$validator->errors()]);
        }

        $data = $validator->validated();
        $user = $request->user();
        $key = $data['key'];
        $formattedKey = $this->sanitizeKey($key);
        $configuration_type = ConfigValueType::from($data['type']);
        $value = $configuration_type->getValue($data['value']);

        $user->setConfigValue($formattedKey, $value, $configuration_type);

        return $this->success(null, 'Configuration added successfully', 201);
    }

    private function sanitizeKey($key): mixed
    {
        $formattedKey = strtolower(preg_replace('/\s+/', '_', $key));

        return preg_replace('/[^a-z0-9_]/', '', $formattedKey);
    }

    public function update(Request $request, $key): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'type' => 'required|in:string,int,float,bool,array,json,date',
        ]);

        if ($validator->fails()) {
            return $this->failure('Validation failed.', 422, [$validator->errors()]);
        }

        $data = $validator->validated();
        $user = $request->user();

        $formattedKey = $this->sanitizeKey($key);

        // Check if the configuration exists
        $configuration = $user->getConfig($formattedKey);

        if (! $configuration) {
            return $this->failure('Configuration not found.', 404);
        }

        // Update the configuration
        $configuration_type = ConfigValueType::from($data['type']);
        $value = $configuration_type->getValue($data['value']);
        $config = $user->setConfigValue($key, $value, $configuration_type);

        return $this->success($config, 'Configuration updated successfully');
    }

    public function destroy(Request $request, $key): JsonResponse
    {
        $user = $request->user();

        $formattedKey = $this->sanitizeKey($key);

        // Check if the configuration exists
        $configuration = $user->getConfig($formattedKey);

        if (! $configuration) {
            return $this->failure('Configuration not found.', 404);
        }

        // Delete the configuration
        $configuration->delete();

        return $this->success(null, 'Configuration deleted successfully');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $configurations = $user->configurations()->get();

        return $this->success($configurations, 'Configurations retrieved successfully');
    }
}

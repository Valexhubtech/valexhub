<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wave\CoolifyServer;

class CoolifyServerSeeder extends Seeder
{
    public function run(): void
    {
        CoolifyServer::updateOrCreate(
            ['name' => 'Local Coolify (VPS 1)'],
            [
                'api_url' => 'http://localhost:3000',
                'api_token' => 'n3GlkYaxeVaEx2wN4824hC8abZJJIsnjHGVzQsDV8b4642a9',
                'coolify_server_uuid' => 'mweh2i2nb6lno55bpveef1t1',
                'coolify_project_uuid' => 'jzgwwn01j55iyj29cvwlyz9z',
                'coolify_environment_name' => 'production',
                'max_deployments' => 20,
                'status' => 'active',
                'sort_order' => 0,
                'notes' => 'Primary local Coolify instance for development and testing.',
            ]
        );
    }
}

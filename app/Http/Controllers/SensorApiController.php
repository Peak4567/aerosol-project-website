<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorApiController extends Controller
{
    private const SENSOR_COLUMNS = [
        'sht40_temp' => ['column' => 'temperature', 'label' => 'Temperature'],
        'sht40_humi' => ['column' => 'humidity', 'label' => 'Humidity'],
        'tds' => ['column' => 'tds_value', 'label' => 'TDS'],
        'ec' => ['column' => 'ec_value', 'label' => 'EC'],
        'ph' => ['column' => 'ph_value', 'label' => 'pH'],
    ];

    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        try {
            if ($type === 'all') {
                $rows = DB::select("
                    SELECT create_at, tds_value, ec_value, temperature, humidity, ph_value
                    FROM (SELECT * FROM sensor ORDER BY id DESC LIMIT 100) AS latest
                    ORDER BY id ASC
                ");

                $labels = [];
                $datasets = [
                    'tds' => [],
                    'ec' => [],
                    'sht40_temp' => [],
                    'sht40_humi' => [],
                    'ph' => [],
                ];

                foreach ($rows as $row) {
                    $labels[] = Carbon::parse($row->create_at)->format('H:i:s');
                    $datasets['tds'][] = (float) $row->tds_value;
                    $datasets['ec'][] = (float) $row->ec_value;
                    $datasets['sht40_temp'][] = (float) $row->temperature;
                    $datasets['sht40_humi'][] = (float) $row->humidity;
                    $datasets['ph'][] = (float) $row->ph_value;
                }

                return response()->json([
                    'labels' => $labels,
                    'datasets' => $datasets,
                ]);
            }

            if (!isset(self::SENSOR_COLUMNS[$type])) {
                return response()->json([
                    'labels' => [],
                    'data' => [],
                    'datasetLabel' => 'Invalid Sensor',
                ], 400);
            }

            $config = self::SENSOR_COLUMNS[$type];
            $col = $config['column'];

            $rows = DB::select("
                SELECT create_at, {$col} AS val
                FROM (SELECT create_at, {$col}, id FROM sensor ORDER BY id DESC LIMIT 100) AS latest
                ORDER BY id ASC
            ");

            $labels = [];
            $data = [];

            foreach ($rows as $row) {
                $labels[] = Carbon::parse($row->create_at)->format('d/m/Y H:i:s');
                $data[] = (float) $row->val;
            }

            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'datasetLabel' => $config['label'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $data = [];

        if ($request->filled('ph_value')) {
            $data['ph_value'] = (float) $request->input('ph_value');
        }
        if ($request->filled('tds_value')) {
            $data['tds_value'] = (float) $request->input('tds_value');
        }
        if ($request->filled('ec_value')) {
            $data['ec_value'] = (float) $request->input('ec_value');
        }
        if ($request->filled('temperature') && (float) $request->input('temperature') > -999) {
            $data['temperature'] = (float) $request->input('temperature');
        }
        if ($request->filled('humidity') && (float) $request->input('humidity') > -999) {
            $data['humidity'] = (float) $request->input('humidity');
        }

        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No valid sensor data received.',
            ], 400);
        }

        $data['create_at'] = Carbon::now();

        try {
            $sensor = Sensor::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data inserted successfully.',
                'id' => $sensor->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to insert data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function simulate()
    {
        $temp = round((2500 + mt_rand(0, 1000)) / 100, 2);
        $humid = round((5000 + mt_rand(0, 3000)) / 100, 2);
        $tds = round(mt_rand(100, 600));
        $ec = round(mt_rand(50, 200) / 100, 2);
        $ph = round((600 + mt_rand(0, 200)) / 100, 2);
        $now = Carbon::now();

        try {
            Sensor::create([
                'temperature' => $temp,
                'humidity' => $humid,
                'tds_value' => $tds,
                'ec_value' => $ec,
                'ph_value' => $ph,
                'create_at' => $now,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'time' => $now->toISOString(),
                    'temp' => $temp,
                    'humid' => $humid,
                    'ph' => $ph,
                    'ec' => $ec,
                    'tds' => $tds,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function health()
    {
        try {
            DB::connection()->getPdo();
            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => Carbon::now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'database' => 'disconnected',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function settings()
    {
        return response()->json(Setting::getAll());
    }
}


"use client";

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Legend,
} from "chart.js";
import { Line } from "react-chartjs-2";
import { SENSOR_CONFIG, SENSOR_KEYS, type AllSensorsResponse } from "./sensor-config";

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend);

interface SensorChartProps {
  data?: AllSensorsResponse;
}

export function SensorChart({ data }: SensorChartProps) {
  const labels = data?.labels ?? [];

  const chartData = {
    labels,
    datasets: SENSOR_KEYS.map((key) => {
      const config = SENSOR_CONFIG[key];
      return {
        label: config.label,
        data: data?.datasets?.[key] ?? [],
        borderColor: config.color,
        backgroundColor: `${config.color}1A`,
        fill: false,
        borderWidth: 2,
        tension: 0.4,
        pointRadius: 0,
        pointHoverRadius: 5,
      };
    }),
  };

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: false },
      x: {
        ticks: { maxRotation: 0, minRotation: 0, autoSkip: true, maxTicksLimit: 10 },
      },
    },
    plugins: {
      legend: { display: true, position: "top" as const },
    },
    interaction: { intersect: false, mode: "index" as const },
  };

  return (
    <div className="h-[340px]">
      <Line data={chartData} options={options} />
    </div>
  );
}

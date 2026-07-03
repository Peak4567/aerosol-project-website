"use client";

interface WaterTankCardProps {
  currentWater: number;
  tankCapacity: number;
  minThreshold: number;
  daysLeftText: string;
  refillDateText: string;
  depletionRateText: string;
  onCapacityChange: (value: number) => void;
  onThresholdChange: (value: number) => void;
}

export function WaterTankCard({
  currentWater,
  tankCapacity,
  minThreshold,
  daysLeftText,
  refillDateText,
  depletionRateText,
  onCapacityChange,
  onThresholdChange,
}: WaterTankCardProps) {
  const percent = Math.min(100, Math.max(0, (currentWater / tankCapacity) * 100));
  const isLow = currentWater <= minThreshold;

  return (
    <div className="card rounded-2xl border-l-4 border-blue-500 p-6 shadow-lg">
      <div className="mb-4 flex items-center justify-between">
        <h2 className="text-xl font-bold text-blue-900">
          1. ระบบคำนวณน้ำและวันเติม
        </h2>
      </div>
      <div className="grid grid-cols-1 items-center gap-8 md:grid-cols-3">
        <div className="flex flex-col items-center">
          <div className="relative mx-auto h-[140px] w-[100px] overflow-hidden rounded-b-2xl border-4 border-blue-800 bg-indigo-100">
            <div
              className="absolute bottom-0 left-0 w-full bg-gradient-to-t from-blue-500 to-blue-400 opacity-80 transition-all duration-500"
              style={{ height: `${percent}%` }}
            />
            <div className="absolute inset-0 z-10 flex items-center justify-center">
              <span className="text-2xl font-bold text-blue-900 drop-shadow-sm">
                {percent.toFixed(0)}%
              </span>
            </div>
          </div>
          <p className="mt-2 text-sm text-gray-500">
            ความจุ: <span>{tankCapacity}</span> ลิตร
          </p>
        </div>
        <div className="col-span-2 space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div className="rounded-lg bg-blue-50 p-3">
              <p className="text-xs text-gray-500">ปัจจุบัน</p>
              <p className="text-xl font-bold text-blue-700">
                {currentWater.toFixed(1)} ลิตร
              </p>
            </div>
            <div className="rounded-lg bg-blue-50 p-3">
              <p className="text-xs text-gray-500">อัตราใช้</p>
              <p className="text-xl font-bold text-blue-700">
                {depletionRateText} ลิตร/วัน
              </p>
            </div>
          </div>
          <div className="flex items-center space-x-4">
            <div className="flex-grow">
              <label className="mb-1 block text-xs font-bold text-gray-600">
                ขนาดถัง
              </label>
              <input
                type="number"
                value={tankCapacity}
                onChange={(e) => onCapacityChange(parseFloat(e.target.value) || 0)}
                className="w-full rounded border p-2 text-center"
              />
            </div>
            <div className="flex-grow">
              <label className="mb-1 block text-xs font-bold text-gray-600">
                เตือนเมื่อเหลือ
              </label>
              <input
                type="number"
                value={minThreshold}
                onChange={(e) => onThresholdChange(parseFloat(e.target.value) || 0)}
                className="w-full rounded border p-2 text-center"
              />
            </div>
          </div>
          <div className="border-t border-gray-200 pt-2 text-center">
            <p className="text-xs text-gray-500">หมดใน</p>
            <p
              className={`text-3xl font-extrabold ${
                isLow ? "text-red-600" : "text-blue-800"
              }`}
            >
              {isLow ? "เติมทันที!" : daysLeftText}
            </p>
            <p className="text-xs text-blue-600">
              {isLow ? "ระดับน้ำต่ำ" : refillDateText}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

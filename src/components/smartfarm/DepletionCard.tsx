"use client";

interface MetricState {
  current: number;
  minThreshold: number;
  daysLeftText: string;
  rateText: string;
}

interface DepletionCardProps {
  tds: MetricState;
  ec: MetricState;
  onTdsThresholdChange: (value: number) => void;
  onEcThresholdChange: (value: number) => void;
  onSave: () => void;
}

export function DepletionCard({
  tds,
  ec,
  onTdsThresholdChange,
  onEcThresholdChange,
  onSave,
}: DepletionCardProps) {
  const tdsLow = tds.current <= tds.minThreshold;
  const ecLow = ec.current <= ec.minThreshold;

  return (
    <div className="card rounded-2xl border-l-4 border-indigo-500 p-6 shadow-lg">
      <div className="mb-4 flex items-center justify-between">
        <div>
          <h2 className="text-xl font-bold text-indigo-900">2. ระบบคำนวณปุ๋ย</h2>
          <p className="mt-1 text-sm text-gray-600">คำนวณจาก EC และ TDS</p>
        </div>
        <button
          onClick={onSave}
          className="rounded bg-indigo-600 px-3 py-1 text-xs text-white shadow"
        >
          บันทึกค่า
        </button>
      </div>
      <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div className="rounded-xl border border-purple-100 bg-purple-50 p-4">
          <h3 className="mb-3 text-sm font-bold text-purple-800">
            TDS Prediction
          </h3>
          <div className="mb-1 flex justify-between text-sm">
            <span className="text-gray-600">ปัจจุบัน:</span>
            <span className="font-bold text-gray-800">{tds.current}</span>
          </div>
          <div className="mb-2 flex justify-between text-sm">
            <span className="text-gray-600">ลดลง:</span>
            <span className="font-bold text-purple-600">{tds.rateText}</span>
          </div>
          <div className="mb-2">
            <label className="text-xs font-bold text-gray-600">Min TDS</label>
            <input
              type="number"
              value={tds.minThreshold}
              onChange={(e) => onTdsThresholdChange(parseFloat(e.target.value) || 0)}
              className="w-full rounded border p-1 text-sm"
            />
          </div>
          <div className="text-center">
            <p className="text-xs text-gray-500">เติมใน</p>
            <p
              className={`text-xl font-bold ${
                tdsLow ? "text-red-600" : "text-purple-700"
              }`}
            >
              {tdsLow ? "เติมทันที!" : tds.daysLeftText}
            </p>
          </div>
        </div>
        <div className="rounded-xl border border-green-100 bg-green-50 p-4">
          <h3 className="mb-3 text-sm font-bold text-green-800">
            EC Prediction
          </h3>
          <div className="mb-1 flex justify-between text-sm">
            <span className="text-gray-600">ปัจจุบัน:</span>
            <span className="font-bold text-gray-800">
              {ec.current.toFixed(2)}
            </span>
          </div>
          <div className="mb-2 flex justify-between text-sm">
            <span className="text-gray-600">ลดลง:</span>
            <span className="font-bold text-green-600">{ec.rateText}</span>
          </div>
          <div className="mb-2">
            <label className="text-xs font-bold text-gray-600">Min EC</label>
            <input
              type="number"
              step={0.1}
              value={ec.minThreshold}
              onChange={(e) => onEcThresholdChange(parseFloat(e.target.value) || 0)}
              className="w-full rounded border p-1 text-sm"
            />
          </div>
          <div className="text-center">
            <p className="text-xs text-gray-500">เติมใน</p>
            <p
              className={`text-xl font-bold ${
                ecLow ? "text-red-600" : "text-green-700"
              }`}
            >
              {ecLow ? "เติมทันที!" : ec.daysLeftText}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

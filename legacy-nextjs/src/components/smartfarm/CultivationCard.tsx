"use client";

interface CultivationCardProps {
  status: "idle" | "running" | "paused";
  elapsedSeconds: number;
  onStart: () => void;
  onStop: () => void;
  onReset: () => void;
}

function pad(value: number) {
  return value.toString().padStart(2, "0");
}

export function CultivationCard({
  status,
  elapsedSeconds,
  onStart,
  onStop,
  onReset,
}: CultivationCardProps) {
  const days = Math.floor(elapsedSeconds / 86400);
  const hours = Math.floor((elapsedSeconds % 86400) / 3600);
  const minutes = Math.floor((elapsedSeconds % 3600) / 60);
  const seconds = Math.floor(elapsedSeconds % 60);

  return (
    <div className="card rounded-2xl p-6 text-center shadow-lg">
      <h2 className="mb-2 text-xl font-bold">สถานะการเพาะปลูก</h2>

      {status === "idle" ? (
        <button
          onClick={onStart}
          className="mt-4 w-full rounded-xl bg-green-500 px-4 py-2 font-bold text-white shadow-md transition hover:bg-green-600"
        >
          เริ่มการเพาะปลูก
        </button>
      ) : (
        <div className="mt-3">
          <div className="text-4xl font-bold text-blue-600">
            <span>{days}</span>{" "}
            <span className="text-lg text-gray-500">วัน</span>
          </div>
          <div className="mt-2 flex justify-center space-x-2 font-mono text-xl text-gray-600">
            <span>{pad(hours)}</span>:<span>{pad(minutes)}</span>:
            <span>{pad(seconds)}</span>
          </div>
          <div className="mt-4 space-y-2">
            {status === "running" ? (
              <button
                onClick={onStop}
                className="w-full rounded-xl bg-red-500 px-4 py-2 font-bold text-white shadow-md transition hover:bg-red-600"
              >
                หยุดการทำงาน
              </button>
            ) : (
              <div className="grid grid-cols-2 gap-2">
                <button
                  onClick={onStart}
                  className="rounded-xl bg-green-500 px-4 py-2 font-bold text-white shadow-md transition hover:bg-green-600"
                >
                  ทำงานต่อ
                </button>
                <button
                  onClick={onReset}
                  className="rounded-xl bg-gray-500 px-4 py-2 font-bold text-white shadow-md transition hover:bg-gray-600"
                >
                  รีเซ็ต
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}

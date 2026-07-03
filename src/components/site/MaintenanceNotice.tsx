export function MaintenanceNotice() {
  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4 text-center"
      style={{ fontFamily: "var(--font-body)" }}
    >
      <div className="w-full max-w-md rounded-xl bg-white p-8 shadow-lg">
        <div className="mb-4 text-6xl">🛠️</div>
        <h1 className="mb-2 text-2xl font-bold text-gray-800">
          เว็บไซต์ปิดปรับปรุงชั่วคราว
        </h1>
        <p className="mb-6 text-gray-600">
          เรากำลังเร่งแก้ไขปัญหาที่เกิดขึ้น โปรดรอ 5-10 นาที
        </p>
        <div className="mb-4 h-2.5 w-full rounded-full bg-gray-200">
          <div className="h-2.5 w-3/4 rounded-full bg-indigo-600" />
        </div>
        <p className="mt-4 text-xs text-gray-400">
          &copy; {new Date().getFullYear()} AI Aerosol
        </p>
      </div>
    </div>
  );
}

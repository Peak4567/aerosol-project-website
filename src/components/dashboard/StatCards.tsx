import type { AllSensorsResponse } from "./sensor-config";

interface StatCardsProps {
  data?: AllSensorsResponse;
}

function last(values: number[] | undefined) {
  if (!values || values.length === 0) return "--";
  return values[values.length - 1];
}

export function StatCards({ data }: StatCardsProps) {
  const temp = last(data?.datasets?.sht40_temp);
  const humi = last(data?.datasets?.sht40_humi);
  const tds = last(data?.datasets?.tds);
  const ec = last(data?.datasets?.ec);
  const ph = last(data?.datasets?.ph);

  return (
    <div className="grid grid-cols-2 gap-6">
      <div className="flex items-center justify-between rounded-xl bg-white p-5 shadow-md">
        <div>
          <p className="text-sm text-gray-400">Temperature</p>
          <p className="mt-1 text-2xl font-bold text-gray-800">{temp} °C</p>
        </div>
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-600">
          <i className="fa-solid fa-temperature-half" />
        </div>
      </div>

      <div className="flex items-center justify-between rounded-xl bg-white p-5 shadow-md">
        <div>
          <p className="text-sm text-gray-400">Humidity</p>
          <p className="mt-1 text-2xl font-bold text-gray-800">{humi} %RH</p>
        </div>
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-xl text-blue-600">
          <i className="fa-solid fa-droplet-degree" />
        </div>
      </div>

      <div className="flex items-center justify-between rounded-xl bg-white p-5 shadow-md">
        <div>
          <p className="text-sm text-gray-400">
            Total Dissolved Solids (TDS)
          </p>
          <p className="mt-1 text-2xl font-bold text-gray-800">{tds} ppm</p>
        </div>
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-xl text-teal-600">
          <i className="fa-solid fa-flask-vial" />
        </div>
      </div>

      <div className="flex items-center justify-between rounded-xl bg-white p-5 shadow-md">
        <div>
          <p className="text-sm text-gray-400">
            Electrical Conductivity (EC)
          </p>
          <p className="mt-1 text-2xl font-bold text-gray-800">{ec} µS/cm</p>
        </div>
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-xl text-purple-600">
          <i className="fa-solid fa-moon-over-sun" />
        </div>
      </div>

      <div className="col-span-2 flex items-center justify-between rounded-xl bg-white p-5 shadow-md">
        <div>
          <p className="text-sm text-gray-400">Potential of Hydrogen (pH)</p>
          <p className="mt-1 text-2xl font-bold text-gray-800">{ph}</p>
        </div>
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-xl text-red-600">
          <i className="fa-solid fa-atom-simple" />
        </div>
      </div>
    </div>
  );
}

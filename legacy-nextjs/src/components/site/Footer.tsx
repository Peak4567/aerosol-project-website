import Image from "next/image";

export function Footer() {
  return (
    <footer className="bg-[#4a5c42]">
      <div className="relative bg-cover bg-center">
        <div className="absolute inset-0 bg-black/60" />

        <div className="container relative z-10 mx-auto px-6 py-16">
          <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div>
              <a href="#home" className="flex items-center gap-2">
                <Image
                  src="/img/aerosol_logo.png"
                  alt="Aerosol Logo"
                  width={48}
                  height={48}
                  className="h-12 w-12"
                />
                <span className="text-2xl font-bold text-white">Aerosol</span>
              </a>
            </div>

            <div>
              <h3 className="mb-4 text-xl font-bold text-white">หน้าเว็บ</h3>
              <ul className="space-y-2">
                {[
                  ["#home", "หน้าหลัก"],
                  ["#about", "เกี่ยวกับเรา"],
                  ["#what", "ข้อสงสัย"],
                  ["#myteam", "คณะผู้จัดทำ"],
                ].map(([href, label]) => (
                  <li key={href}>
                    <a
                      href={href}
                      className="text-gray-300 transition-colors hover:text-white"
                    >
                      {label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>

            <div>
              <h3 className="mb-4 text-xl font-bold text-white">ช่องทางติดต่อ</h3>
              <ul className="space-y-3 text-gray-300">
                <li className="flex items-center gap-3">
                  <i className="fa-solid fa-phone w-5 shrink-0" />
                  <span>08 1567 4654</span>
                </li>
                <li className="flex items-center gap-3">
                  <i className="fa-solid fa-envelope w-5 shrink-0" />
                  <span>aerosol@gmail.com</span>
                </li>
                <li className="flex items-start gap-3">
                  <i className="fa-solid fa-location-dot mt-1 w-5 shrink-0" />
                  <span>โรงเรียนชลประทานวิทยา นนทบุรี 11120</span>
                </li>
              </ul>
            </div>

            <div>
              <h3 className="mb-4 text-xl font-bold text-white">ติดต่องาน</h3>
              <div className="relative">
                <input
                  type="email"
                  placeholder="Email Address"
                  className="w-full rounded-full border border-white/20 bg-white/10 py-3 pl-4 pr-12 text-white placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-white/50"
                />
                <button className="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 transition-colors hover:bg-white/30">
                  <i className="fa-solid fa-arrow-right text-white" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-6 py-4">
        <div className="flex flex-col items-center justify-between text-sm text-gray-300 sm:flex-row">
          <p>© Copyright {new Date().getFullYear()} By Aeroponic CPW</p>
          <div className="mt-4 flex gap-4 sm:mt-0">
            {["facebook-f", "twitter", "instagram"].map((icon) => (
              <a
                key={icon}
                href="#"
                className="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
              >
                <i className={`fa-brands fa-${icon} text-white`} />
              </a>
            ))}
          </div>
        </div>
      </div>
    </footer>
  );
}

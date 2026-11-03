import Image from "next/image";
import { Reveal } from "@/components/site/Reveal";

const TEAM_MEMBERS = [
  {
    name: "ณัฐนันท์ สุมาลุย",
    role: "หัวหน้าโครงงาน",
    grade: "มัธยมศึกษาปีที่ 6",
    img: "/img/profile/ju.png",
    accent: "hover:border-yellow-400",
  },
  {
    name: "ศรัณยกร เทพสุนทร",
    role: "โปรแกรมเมอร์",
    grade: "มัธยมศึกษาปีที่ 5",
    img: "/img/profile/peak.png",
    accent: "hover:border-red-400",
  },
  {
    name: "ศุภกรณ์ สุภิมารส",
    role: "ฝ่ายฮาร์ดแวร์",
    grade: "มัธยมศึกษาปีที่ 5",
    img: "/img/profile/walid.png",
    accent: "hover:border-blue-400",
  },
  {
    name: "พงศภาวิน ปานปิ่น",
    role: "ฝ่ายฮาร์ดแวร์",
    grade: "มัธยมศึกษาปีที่ 5",
    img: "/img/profile/rew.png",
    accent: "hover:border-green-400",
  },
  {
    name: "กอบชัย ศรีทองกลาง",
    role: "ฝ่ายปัญญาประดิษฐ์",
    grade: "มัธยมศึกษาปีที่ 3",
    img: "/img/profile/kun2.png",
    accent: "hover:border-purple-400",
  },
];

const ADVISORS = [
  {
    name: "วีระกิตติ์ รฐาโชคนิธิธนาดุล",
    role: "หัวหน้าศูนย์สตรีม/อาจารย์ที่ปรึกษา",
    img: "/img/profile/Adam.png",
  },
  {
    name: "ชัยรัตน์ ศรีวิลาส",
    role: "ตำแหน่ง อาจารย์ที่ปรึกษา",
    img: "/img/profile/doom.png",
  },
  {
    name: "พิจิตรา หงษ์จันทร์",
    role: "ตำแหน่ง อาจารย์ที่ปรึกษา",
    img: "/img/profile/may.png",
  },
];

export function LandingContent() {
  return (
    <>
      <section
        id="home"
        className="relative flex h-screen items-start justify-center pt-[150px] text-center"
      >
        <div className="absolute inset-0 -z-10 h-full w-full">
          <Image
            src="/img/background.png"
            alt="Farm background"
            fill
            priority
            className="object-cover brightness-75"
          />
        </div>
        <div className="relative z-10 w-[92%] max-w-[595px] px-4">
          <h1 className="text-4xl font-bold text-white drop-shadow-sm md:text-6xl">
            Aerosol
          </h1>
          <h2 className="text-3xl font-bold text-white drop-shadow-sm md:text-4xl">
            AeroFogPonic Farming
          </h2>
          <p className="pt-5 text-white/90">
            สัมผัสนวัตกรรมการเกษตรไร้ดิน ที่เปลี่ยนอากาศให้กลายเป็นพื้นที่เพาะปลูกคุณภาพสูง
            ให้ผลผลิตที่โตเร็วขึ้น สะอาดกว่า และประหยัดน้ำสูงสุดถึง 95%
          </p>
          <a
            href="#about"
            className="mt-8 inline-block rounded-full bg-black px-10 py-3 font-semibold text-white transition-colors duration-300 hover:bg-gray-800"
          >
            ดูเพิ่มเติม
          </a>
        </div>
        <div className="absolute bottom-[200px] left-0 w-full overflow-hidden leading-none">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 1200 120"
            preserveAspectRatio="none"
            className="relative block h-[150px] w-full"
          >
            <path
              d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
              className="fill-current text-white"
            />
          </svg>
        </div>
      </section>

      <section
        id="about"
        className="relative -mt-[200px] bg-white pb-20 pt-10 text-black"
      >
        <div className="container mx-auto px-4 text-center">
          <Reveal>
            <h2 className="mb-4 text-4xl font-bold md:text-5xl">
              Aerosol Project
            </h2>
            <p className="mb-16 text-lg text-gray-600">
              ส่วนประกอบพัฒนาสำคัญสำหรับโพรเจ็กต์เรา
            </p>
          </Reveal>

          <Reveal delay={0.1}>
            <div className="mx-auto max-w-4xl rounded-2xl bg-[#F5E6BE] p-8 text-gray-800 shadow-lg">
              <div className="flex flex-col items-center justify-around space-y-8 md:flex-row md:space-y-0">
                <div className="flex w-full flex-col items-center text-center md:w-1/3">
                  <i className="fa-solid fa-potato pb-2 text-7xl" />
                  <p className="text-3xl font-bold">มันฝรั่ง</p>
                  <p className="text-base">พืชใช้ในการเพาะปลูก</p>
                </div>
                <div className="flex w-full flex-col items-center text-center md:w-1/3">
                  <i className="fa-solid fa-square-terminal pb-2 text-7xl" />
                  <p className="text-3xl font-bold">แผงควบคุม</p>
                  <p className="text-base">สำหรับแสดงผลข้อมูล</p>
                </div>
                <div className="flex w-full flex-col items-center text-center md:w-1/3">
                  <i className="fa-solid fa-user-robot pb-2 text-7xl" />
                  <p className="text-3xl font-bold">ปัญญาประดิษฐ์</p>
                  <p className="text-base">ควบคุมอัตโนมัติ</p>
                </div>
              </div>
            </div>
          </Reveal>
        </div>
      </section>

      <section className="py-16 md:py-24">
        <div className="container mx-auto space-y-20 px-6">
          <div className="flex flex-col items-center gap-12 lg:flex-row lg:gap-20">
            <Reveal direction="left" className="relative w-full lg:w-5/12">
              <div className="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-yellow-400" />
              <div className="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-yellow-300 opacity-70" />
              <Image
                src="/img/1.png"
                alt="ปลูกมันฝรั่ง"
                width={600}
                height={400}
                className="relative z-10 w-full rounded-lg shadow-xl"
              />
            </Reveal>
            <Reveal direction="right" className="w-full lg:w-7/12">
              <h2 className="text-3xl font-bold text-gray-800 md:text-4xl">
                ทำไมเราถึงเลือกปลูกมันฝรั่ง?
              </h2>
              <p className="mt-4 text-lg text-gray-600">
                ประเทศไทยกำลังเผชิญปัญหาอุทกภัย โดยเฉพาะในภาคเหนือ ซึ่งเป็นแหล่งปลูกมันฝรั่งสำคัญ
                ส่งผลให้ดินเสียหายและกระทบต่อเศรษฐกิจและผลผลิตการเกษตร คณะผู้จัดทำจึงพัฒนานวัตกรรมนี้
                เพื่อช่วยให้เพาะปลูกได้แม้ในสภาวะไม่เอื้ออำนวย พร้อมสนับสนุนเกษตรกรรมยั่งยืนในอนาคต
              </p>
              <a
                href="#what"
                className="mt-6 inline-block rounded-lg bg-yellow-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-yellow-600"
              >
                อ่านเพิ่มเติม
              </a>
            </Reveal>
          </div>

          <div className="flex flex-col items-center gap-12 lg:flex-row-reverse lg:gap-20">
            <Reveal direction="right" className="relative w-full lg:w-5/12">
              <div className="absolute -right-4 -top-4 z-0 h-full w-full rotate-2 rounded-lg border-4 border-red-400" />
              <div className="absolute -bottom-6 -left-6 h-20 w-20 rounded-full bg-red-300 opacity-70" />
              <Image
                src="/img/2.jpg"
                alt="วัตถุประสงค์"
                width={600}
                height={400}
                className="relative z-10 w-full rounded-lg shadow-xl"
              />
            </Reveal>
            <Reveal direction="left" className="w-full lg:w-7/12">
              <h2 className="text-3xl font-bold text-gray-800 md:text-4xl">
                วัตถุประสงค์การปลูกมันฝรั่ง
              </h2>
              <p className="mt-4 text-lg text-gray-600">
                เพื่อเพิ่มประสิทธิภาพการผลิต ลดการใช้ทรัพยากร จัดการความเสี่ยงจากสภาพแวดล้อม
                ส่งเสริมความยั่งยืน สร้างความสะดวกให้เกษตรกร และผลิตมันฝรั่งคุณภาพสูงตอบสนอง
                ความต้องการของตลาด
              </p>
              <a
                href="#what"
                className="mt-6 inline-block rounded-lg bg-red-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-red-600"
              >
                อ่านเพิ่มเติม
              </a>
            </Reveal>
          </div>

          <div className="flex flex-col items-center gap-12 lg:flex-row lg:gap-20">
            <Reveal direction="left" className="relative w-full lg:w-5/12">
              <div className="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-blue-400" />
              <div className="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-blue-300 opacity-70" />
              <Image
                src="/img/4.jpg"
                alt="SmartFarm IoT"
                width={600}
                height={400}
                className="relative z-10 w-full rounded-lg shadow-xl"
              />
            </Reveal>
            <Reveal direction="right" className="w-full lg:w-7/12">
              <h2 className="text-3xl font-bold text-gray-800 md:text-4xl">
                ระบบ SmartFarm IOT คืออะไร?
              </h2>
              <p className="mt-4 text-lg text-gray-600">
                คือการนำเทคโนโลยี Internet of Things (IoT) มาประยุกต์ใช้ในภาคเกษตรกรรมเพื่อเพิ่ม
                ประสิทธิภาพและความยั่งยืน โดยระบบนี้ช่วยให้เกษตรกรสามารถตรวจสอบและควบคุมปัจจัยต่างๆ
                ผ่านเซ็นเซอร์และอุปกรณ์อัจฉริยะที่เชื่อมต่อกัน
              </p>
              <a
                href="#what"
                className="mt-6 inline-block rounded-lg bg-blue-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-blue-600"
              >
                อ่านเพิ่มเติม
              </a>
            </Reveal>
          </div>
        </div>
      </section>

      <section id="what" className="py-16 md:py-24">
        <div className="container mx-auto px-6">
          <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <Reveal direction="left" className="relative">
              <div className="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-yellow-400" />
              <div className="relative z-10 overflow-hidden rounded-lg shadow-xl">
                <Image
                  src="/img/smartfarm44.jpg"
                  alt="Organic Farmer"
                  width={700}
                  height={500}
                  className="h-auto w-full"
                />
              </div>
            </Reveal>

            <Reveal direction="right">
              <p className="text-2xl font-medium text-gray-500">
                ทำไมเราถึงเลือกใช้?
              </p>
              <h2 className="mt-1 text-xl font-bold text-gray-800 md:text-4xl">
                AeroFogPonic Farming
              </h2>
              <p className="mt-6 leading-relaxed text-gray-600">
                ระบบปลูกพืชไร้ดินขั้นสูง ที่ให้สารอาหารแก่รากพืชในรูปแบบของ &quot;หมอก&quot;
                ซึ่งมีอนุภาคเล็กและละเอียดกว่าการพ่นสเปรย์แบบแอโรโพนิกส์ทั่วไป
              </p>

              <div className="mt-8 grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                {[
                  { icon: "fa-seedling", label: "ต้นพันธ์ปลอดเชื้อโรค" },
                  { icon: "fa-cloud-rain", label: "ควบคุมสภาพแวดล้อม" },
                  { icon: "fa-pot-food", label: "สร้างความมั่นคงทางอาหาร" },
                  { icon: "fa-shield-check", label: "ปลอดสารพิษ" },
                ].map((item) => (
                  <div key={item.label} className="flex items-center">
                    <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-700">
                      <i className={`fa-solid ${item.icon} text-gray-100`} />
                    </div>
                    <span className="ml-4 font-semibold text-gray-700">
                      {item.label}
                    </span>
                  </div>
                ))}
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      <section
        id="myteam"
        className="relative overflow-hidden bg-gray-50 py-16 md:py-24"
      >
        <div className="absolute left-0 top-0 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-yellow-200 opacity-30 mix-blend-multiply blur-3xl" />
        <div className="absolute bottom-0 right-0 h-64 w-64 translate-x-1/2 translate-y-1/2 rounded-full bg-blue-200 opacity-30 mix-blend-multiply blur-3xl" />

        <div className="container relative z-10 mx-auto px-6">
          <Reveal className="mb-12 text-center">
            <span className="text-sm font-semibold uppercase tracking-wider text-yellow-600">
              Our Team
            </span>
            <h2 className="mt-2 text-3xl font-bold text-gray-800 md:text-5xl">
              คณะผู้จัดทำ
            </h2>
            <div className="mx-auto mt-4 h-1 w-24 rounded-full bg-yellow-400" />
          </Reveal>

          <div className="mb-20 flex flex-wrap justify-center gap-8">
            {TEAM_MEMBERS.map((member, index) => (
              <Reveal
                key={member.name}
                delay={index * 0.08}
                className={`group w-full min-w-[260px] overflow-hidden rounded-2xl border-b-4 border-transparent bg-white shadow-lg transition-all duration-300 hover:-translate-y-2 ${member.accent} sm:w-[calc(50%-2rem)] lg:w-[calc(33.33%-2rem)] xl:w-[calc(20%-2rem)]`}
              >
                <div className="relative h-48 overflow-hidden">
                  <Image
                    src={member.img}
                    alt={member.name}
                    fill
                    className="object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-black/20 transition-all duration-300 group-hover:bg-black/10" />
                </div>
                <div className="relative p-4 text-center">
                  <div className="relative z-10 mx-auto -mt-14 h-20 w-20 overflow-hidden rounded-full border-4 border-white bg-white shadow-md">
                    <Image
                      src={member.img}
                      alt={member.name}
                      width={80}
                      height={80}
                      className="h-full w-full object-cover"
                    />
                  </div>
                  <h3 className="mt-3 text-lg font-bold text-gray-800">
                    {member.name}
                  </h3>
                  <p className="mb-2 text-xs font-medium uppercase tracking-wide text-yellow-600">
                    {member.role}
                  </p>
                  <p className="mb-3 text-xs text-gray-500">{member.grade}</p>
                </div>
              </Reveal>
            ))}
          </div>

          <Reveal className="mb-10 mt-16 text-center">
            <h3 className="text-2xl font-bold text-gray-800 md:text-3xl">
              ครูที่ปรึกษาโครงงาน
            </h3>
            <div className="mx-auto mt-3 h-1 w-16 rounded-full bg-gray-300" />
          </Reveal>

          <div className="mx-auto grid max-w-5xl grid-cols-1 gap-8 md:grid-cols-3">
            {ADVISORS.map((advisor, index) => (
              <Reveal
                key={advisor.name}
                delay={index * 0.1}
                className="flex flex-col items-center rounded-xl border border-gray-100 bg-white p-6 text-center shadow-md transition-shadow duration-300 hover:shadow-lg"
              >
                <div className="mb-4 h-24 w-24 overflow-hidden rounded-full bg-gray-200 ring-4 ring-gray-50">
                  <Image
                    src={advisor.img}
                    alt={advisor.name}
                    width={96}
                    height={96}
                    className="h-full w-full object-cover"
                  />
                </div>
                <h4 className="text-lg font-bold text-gray-800">
                  {advisor.name}
                </h4>
                <p className="text-sm text-gray-500">{advisor.role}</p>
              </Reveal>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}

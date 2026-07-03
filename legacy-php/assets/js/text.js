const messages = [
  '"นวัตกรรมเครื่องเพาะปลูกมันฝรั่ง"',
  '"นวัตกรรมเพื่ออนาคต"',
];

let index = 0;

function changeText() {
  const changingText = document.getElementById("changingText");
  changingText.textContent = messages[index];
  index = (index + 1) % messages.length; // หมุนวนกลับไปข้อความแรกเมื่อจบ
}

setInterval(changeText, 4000); // เปลี่ยนทุกๆ 2 วินาที (2000 มิลลิวินาที)F

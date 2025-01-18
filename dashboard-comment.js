document.getElementById("prev").addEventListener("click", function () {
  // Tambahkan logika untuk navigasi ke halaman sebelumnya
  console.log("Navigasi ke halaman sebelumnya");
});

document.getElementById("next").addEventListener("click", function () {
  // Tambahkan logika untuk navigasi ke halaman berikutnya
  console.log("Navigasi ke halaman berikutnya");
});

// Tambahkan event listener untuk tombol halaman jika diperlukan
const pageButtons = document.querySelectorAll(
  ".btn-page:not(#prev):not(#next)"
);
pageButtons.forEach((button) => {
  button.addEventListener("click", function () {
    console.log(`Navigasi ke halaman ${this.innerText}`);
  });
});

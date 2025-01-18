function confirmSubmission(event) {
  event.preventDefault(); // Mencegah pengiriman form

  // Ambil nilai dari form
  const name = document.querySelector('input[name="name"]').value;
  const email = document.querySelector('input[name="email"]').value;
  const phone = document.querySelector('input[name="phone"]').value;
  const guestsCount = document.querySelector(
    'input[name="guests_count"]'
  ).value;
  // const foodPreference = document.querySelector(
  //   'input[name="food_preference"]'
  // ).value;
  const notes = document.querySelector('textarea[name="notes"]').value;

  // Buat pesan konfirmasi
  const message =
    `<p>Apakah data Anda sudah benar?</p>` +
    `<p>Nama: ${name}</p>` +
    `<p>Email: ${email}</p>` +
    `<p>Telepon: ${phone}</p>` +
    `<p>Jumlah Tamu: ${guestsCount}</p>` +
    // `<p>Preferensi Makanan: ${foodPreference}</p>` +
    `<p>Catatan: ${notes}`;

  // Tampilkan pesan konfirmasi di modal
  document.getElementById("confirmation-message").innerHTML = message;
  document.getElementById("confirmation-modal").style.display = "block"; // Tampilkan modal
}

// Fungsi untuk menutup modal
function closeModal() {
  document.getElementById("confirmation-modal").style.display = "none"; // Sembunyikan modal
}

// Fungsi untuk memproses form saat konfirmasi
function processForm() {
  document.getElementById("registration-form").submit(); // Kirim form jika dikonfirmasi

  const formRsvp = document.querySelector(".formulir-rsvp");
  const mainDashboard = document.querySelector(".main-dashboard");
  formRsvp.classList.add("d-none");
  mainDashboard.classList.remove("d-none");
}

// Event listener untuk menutup modal
window.onload = function () {
  document.getElementById("close-modal").onclick = closeModal;

  // Menangani aksi setiap tombol
  document.getElementById("cancel-button").onclick = closeModal;
  document.getElementById("confirm-button").onclick = processForm;
};

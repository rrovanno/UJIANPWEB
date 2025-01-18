document.getElementById("logout-button").onclick = function () {
  document.getElementById("logout-modal").style.display = "flex"; // Menampilkan modal
};

document.getElementById("close-logout-modal").onclick = function () {
  document.getElementById("logout-modal").style.display = "none"; // Menyembunyikan modal
};

document.getElementById("cancel-logout-button").onclick = function () {
  document.getElementById("logout-modal").style.display = "none"; // Menyembunyikan modal
};

function logout() {
  window.location.href = "logout.php"; // Arahkan ke halaman logout
}

// Menutup modal dengan mengklik di luar modal
window.onclick = function (event) {
  const modal = document.getElementById("logout-modal");
  if (event.target == modal) {
    modal.style.display = "none"; // Menyembunyikan modal jika mengklik di luar
  }
};

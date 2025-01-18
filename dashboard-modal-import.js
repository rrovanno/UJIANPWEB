const buttonOpenModal = document.getElementById("button-import");
const sectionModalImport = document.getElementById("modal-import");

const closeImportModal = document.getElementById("close-import-modal");

buttonOpenModal.addEventListener("click", (e) => {
  e.preventDefault();
  sectionModalImport.style.display = "block";
});

closeImportModal.addEventListener("click", (e) => {
  e.preventDefault();
  sectionModalImport.style.display = "none";
});

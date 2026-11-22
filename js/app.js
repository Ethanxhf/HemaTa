function formatRupiah(angka) {
    const bulat = Math.round(angka);
    return "Rp " + bulat.toLocaleString("id-ID");
}

function formatLiter(angka) {
    return Math.round(angka).toLocaleString("id-ID") + " Liter";
}

function tandaiSalah(elemen) {
    elemen.classList.add("input-salah");
    elemen.focus();
    setTimeout(function () {
        elemen.classList.remove("input-salah");
    }, 2500);
}

function validasiRegistrasi(form) {
    const nama = form.nama;
    const username = form.username;
    const password = form.password;
    const ulangi = form.ulangi_password;

    if (nama.value.trim() === "") {
        tandaiSalah(nama);
        alert("Nama lengkap wajib diisi!");
        return false;
    }
    if (username.value.trim().length < 4) {
        tandaiSalah(username);
        alert("Username minimal 4 karakter!");
        return false;
    }
    if (password.value.length < 6) {
        tandaiSalah(password);
        alert("Password minimal 6 karakter!");
        return false;
    }
    if (password.value !== ulangi.value) {
        tandaiSalah(ulangi);
        alert("Konfirmasi password tidak sama!");
        return false;
    }
    return true;
}

function validasiLogin(form) {
    if (form.username.value.trim() === "") {
        tandaiSalah(form.username);
        alert("Username wajib diisi!");
        return false;
    }
    if (form.password.value === "") {
        tandaiSalah(form.password);
        alert("Password wajib diisi!");
        return false;
    }
    return true;
}

function validasiListrik(form) {
    const daya = form.daya_va;
    const kwh = form.kwh;

    if (daya.value === "") {
        tandaiSalah(daya);
        alert("Silakan pilih daya terpasang!");
        return false;
    }
    if (kwh.value === "" || parseFloat(kwh.value) <= 0) {
        tandaiSalah(kwh);
        alert("Pemakaian kWh harus lebih dari 0!");
        return false;
    }
    return true;
}

function validasiAir(form) {
    const nominal = form.nominal_rupiah;

    if (nominal.value === "" || parseFloat(nominal.value) <= 0) {
        tandaiSalah(nominal);
        alert("Nominal rupiah harus lebih dari 0!");
        return false;
    }
    return true;
}

function pratinjauListrik(form) {
    const tarifEl = document.getElementById("pratinjau-tarif");
    const totalEl = document.getElementById("pratinjau-total");
    if (!tarifEl || !totalEl) {
        return;
    }
    const pilihan = form.daya_va.options[form.daya_va.selectedIndex];
    const kwh = parseFloat(form.kwh.value) || 0;
    const tarif = pilihan ? parseFloat(pilihan.getAttribute("data-tarif")) || 0 : 0;
    tarifEl.textContent = formatRupiah(tarif) + " / kWh";
    totalEl.textContent = formatRupiah(kwh * tarif);
}

function pratinjauAir(form) {
    const literEl = document.getElementById("pratinjau-liter");
    const nominalEl = document.getElementById("pratinjau-nominal");
    if (!literEl || !nominalEl) {
        return;
    }
    const nominal = parseFloat(form.nominal_rupiah.value) || 0;
    const tarifAir = parseFloat(form.getAttribute("data-tarif-air")) || 1;
    literEl.textContent = formatLiter(nominal / tarifAir * 1000);
    nominalEl.textContent = formatRupiah(nominal);
}

function pasangValidasi() {
    const daftarForm = document.querySelectorAll("form");

    daftarForm.forEach(function (form) {
        const tipe = form.getAttribute("data-tipe");

        if (form.id === "form-register") {
            form.addEventListener("submit", function (event) {
                if (!validasiRegistrasi(form)) {
                    event.preventDefault();
                }
            });
            return;
        }

        if (form.id === "form-login") {
            form.addEventListener("submit", function (event) {
                if (!validasiLogin(form)) {
                    event.preventDefault();
                }
            });
            return;
        }

        if (tipe === "listrik") {
            form.addEventListener("submit", function (event) {
                if (!validasiListrik(form)) {
                    event.preventDefault();
                }
            });
            form.addEventListener("input", function () {
                pratinjauListrik(form);
            });
            form.addEventListener("change", function () {
                pratinjauListrik(form);
            });
            pratinjauListrik(form);
            return;
        }

        if (tipe === "air") {
            form.addEventListener("submit", function (event) {
                if (!validasiAir(form)) {
                    event.preventDefault();
                }
            });
            form.addEventListener("input", function () {
                pratinjauAir(form);
            });
            pratinjauAir(form);
        }
    });
}

document.addEventListener("DOMContentLoaded", pasangValidasi);

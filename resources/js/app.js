// Klinik Maristella Application Core JS

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const searchInput = document.getElementById('search-input');
  const mobileSearchInput = document.getElementById('mobile-search-input');
  const desktopChips = document.querySelectorAll('.desktop-chip');
  const mobileChips = document.querySelectorAll('.mobile-chip');
  const doctorCards = document.querySelectorAll('.doctor-card');
  const toastEl = document.getElementById('app-toast');

  // Booking panel elements (Desktop & Mobile)
  const pavEl = document.getElementById('pav');
  const pnEl = document.getElementById('pn');
  const psEl = document.getElementById('ps');
  const prEl = document.getElementById('pr');
  const ppEl = document.getElementById('pp');
  const pavAvailEl = document.getElementById('pav-avail');
  const pavExpEl = document.getElementById('pav-exp');
  const pavPatientsEl = document.getElementById('pav-patients');

  // Mobile Bottom Sheet
  const mobileSheet = document.getElementById('mobile-detail-sheet');
  const mobileSheetBackdrop = document.getElementById('mobile-sheet-backdrop');
  const mobileSheetClose = document.getElementById('mobile-sheet-close');
  const mpavEl = document.getElementById('m-pav');
  const mpnEl = document.getElementById('m-pn');
  const mpsEl = document.getElementById('m-ps');
  const mprEl = document.getElementById('m-pr');
  const mppEl = document.getElementById('m-pp');
  const mpavAvailEl = document.getElementById('m-pav-avail');
  const mpavExpEl = document.getElementById('m-pav-exp');
  const mpavPatientsEl = document.getElementById('m-pav-patients');

  // State
  let currentCategory = 'all';
  let searchQuery = '';
  let selectedDate = '12';
  let selectedDayName = 'Tu';
  let selectedTime = '01 PM';
  let currentDoctor = null;

  // Initial Selected Doctor from currently selected card
  const selectedCard = document.querySelector('.doctor-card.selected') || document.querySelector('.doctor-card');
  if (selectedCard) {
    currentDoctor = extractDoctorData(selectedCard);
  }

  function extractDoctorData(card) {
    return {
      id: card.dataset.id,
      name: card.dataset.name,
      specialty: card.dataset.specialty,
      category: card.dataset.category,
      rating: card.dataset.rating,
      price: card.dataset.price,
      initials: card.dataset.initials,
      image: card.dataset.image,
      availability: card.dataset.availability || '9AM - 8PM',
      experience: card.dataset.experience || '10 years',
      patients: card.dataset.patients || '3.5k Up',
    };
  }

  // Toast Helper
  let toastTimer = null;
  function showToast(message, iconSvg = '') {
    if (!toastEl) return;
    toastEl.innerHTML = iconSvg ? `<span class="toast-icon">${iconSvg}</span> <span>${message}</span>` : `<span>${message}</span>`;
    toastEl.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toastEl.classList.remove('show');
    }, 2800);
  }
  window.showToast = showToast;

  // Filter Doctors
  function filterDoctors() {
    let visibleCount = 0;
    doctorCards.forEach(card => {
      const matchCat = currentCategory === 'all' || card.dataset.category === currentCategory;
      const haystack = (card.dataset.name + ' ' + card.dataset.specialty).toLowerCase();
      const matchSearch = !searchQuery || haystack.includes(searchQuery);

      if (matchCat && matchSearch) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Handle empty state message
    const emptyState = document.getElementById('doctors-empty');
    if (emptyState) {
      emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  // Category Chip Click Handling
  function setupChips(chips, isMobile = false) {
    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        const cat = chip.dataset.cat;
        currentCategory = cat;

        // Sync both desktop and mobile chips
        desktopChips.forEach(c => c.classList.toggle('active', c.dataset.cat === cat));
        mobileChips.forEach(c => c.classList.toggle('active', c.dataset.cat === cat));

        filterDoctors();
      });
    });
  }
  setupChips(desktopChips, false);
  setupChips(mobileChips, true);

  // Search Input Handling
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim().toLowerCase();
      if (mobileSearchInput) mobileSearchInput.value = e.target.value;
      filterDoctors();
    });
  }
  if (mobileSearchInput) {
    mobileSearchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim().toLowerCase();
      if (searchInput) searchInput.value = e.target.value;
      filterDoctors();
    });
  }

  // Update Detail Panel (Desktop)
  function updateDesktopPanel(doc) {
    if (!doc) return;
    const pavImg = document.getElementById('pav-img');
    if (pavImg && doc.image) {
      pavImg.src = doc.image;
      pavImg.alt = doc.name;
    }
    if (pavEl) pavEl.textContent = doc.initials;
    if (pnEl) pnEl.textContent = doc.name;
    if (psEl) psEl.textContent = doc.specialty;
    if (prEl) prEl.textContent = doc.rating;
    if (ppEl) ppEl.innerHTML = `${doc.price} <span style="font-size:11px;font-weight:400;color:var(--color-text-muted)">/per session</span>`;
    if (pavAvailEl) pavAvailEl.textContent = doc.availability;
    if (pavExpEl) pavExpEl.textContent = doc.experience;
    if (pavPatientsEl) pavPatientsEl.textContent = doc.patients;
  }

  // Update Detail Sheet (Mobile)
  function updateMobileSheet(doc) {
    if (!doc) return;
    const mpavImg = document.getElementById('m-pav-img');
    if (mpavImg && doc.image) {
      mpavImg.src = doc.image;
      mpavImg.alt = doc.name;
    }
    if (mpavEl) mpavEl.textContent = doc.initials;
    if (mpnEl) mpnEl.textContent = doc.name;
    if (mpsEl) mpsEl.textContent = doc.specialty;
    if (mprEl) mprEl.textContent = doc.rating;
    if (mppEl) mppEl.innerHTML = `${doc.price} <span style="font-size:11px;font-weight:400;color:var(--color-text-muted)">/per session</span>`;
    if (mpavAvailEl) mpavAvailEl.textContent = doc.availability;
    if (mpavExpEl) mpavExpEl.textContent = doc.experience;
    if (mpavPatientsEl) mpavPatientsEl.textContent = doc.patients;
  }

  function openMobileSheet() {
    if (mobileSheet && mobileSheetBackdrop) {
      mobileSheet.classList.add('open');
      mobileSheetBackdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeMobileSheet() {
    if (mobileSheet && mobileSheetBackdrop) {
      mobileSheet.classList.remove('open');
      mobileSheetBackdrop.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  if (mobileSheetClose) mobileSheetClose.addEventListener('click', closeMobileSheet);
  if (mobileSheetBackdrop) mobileSheetBackdrop.addEventListener('click', closeMobileSheet);

  // Doctor Card Click Handling
  doctorCards.forEach(card => {
    card.addEventListener('click', () => {
      doctorCards.forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');

      currentDoctor = extractDoctorData(card);
      updateDesktopPanel(currentDoctor);

      // On mobile view (< 768px), open bottom sheet
      if (window.innerWidth < 1024) {
        updateMobileSheet(currentDoctor);
        openMobileSheet();
      }
    });
  });

  // Date selection (Desktop & Mobile)
  const setupDatePicker = (containerSelector) => {
    const containers = document.querySelectorAll(containerSelector);
    containers.forEach(container => {
      const dayButtons = container.querySelectorAll('.day-pill');
      dayButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          selectedDate = btn.dataset.date;
          selectedDayName = btn.dataset.day;
          document.querySelectorAll('.day-pill').forEach(b => {
            b.classList.toggle('active', b.dataset.date === selectedDate);
          });
        });
      });
    });
  };
  setupDatePicker('.days-container');

  // Time slot selection (Desktop & Mobile)
  const setupTimeSlots = (containerSelector) => {
    const containers = document.querySelectorAll(containerSelector);
    containers.forEach(container => {
      const slotButtons = container.querySelectorAll('.slot-pill');
      slotButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          selectedTime = btn.dataset.time;
          document.querySelectorAll('.slot-pill').forEach(b => {
            b.classList.toggle('active', b.dataset.time === selectedTime);
          });
        });
      });
    });
  };
  setupTimeSlots('.slots-container');

  // Booking Registration Modal Elements
  const bookingModalBackdrop = document.getElementById('booking-modal-backdrop');
  const bookingModal = document.getElementById('booking-modal');
  const btnCloseModal = document.getElementById('btn-close-modal');
  const btnCancelBooking = document.getElementById('btn-cancel-booking');
  const btnConfirmBooking = document.getElementById('btn-confirm-booking');
  const modalDocImg = document.getElementById('modal-doc-img');
  const modalDocInitials = document.getElementById('modal-doc-initials');
  const modalDocName = document.getElementById('modal-doc-name');
  const modalDocRating = document.getElementById('modal-doc-rating');
  const modalDocSpecialty = document.getElementById('modal-doc-specialty');
  const modalDocSchedule = document.getElementById('modal-doc-schedule');
  const modalDocPrice = document.getElementById('modal-doc-price');

  const regPatientName = document.getElementById('reg-patient-name');
  const regPatientPhone = document.getElementById('reg-patient-phone');
  const regSymptoms = document.getElementById('reg-symptoms');
  const symptomsError = document.getElementById('symptoms-error');
  const regDuration = document.getElementById('reg-duration');
  const regSeverity = document.getElementById('reg-severity');
  const regNotes = document.getElementById('reg-notes');
  const quickSymptomsChips = document.querySelectorAll('#quick-symptoms-chips .symptom-chip');
  const durationPills = document.querySelectorAll('#duration-selector .duration-pill');
  const severityPills = document.querySelectorAll('#severity-selector .severity-pill');

  // Open & Close Booking Intake Modal
  function openBookingModal() {
    if (!currentDoctor) {
      currentDoctor = {
        id: 3,
        name: 'Dr. Saran Mitchell',
        specialty: 'Orthopedist',
        image: 'images/doctors/doctor-3.png',
        initials: 'SM'
      };
    }

    // Sync modal doctor info
    if (modalDocImg) {
      if (currentDoctor.image) {
        modalDocImg.src = currentDoctor.image;
        modalDocImg.alt = currentDoctor.name;
        modalDocImg.style.display = 'block';
        if (modalDocInitials) modalDocInitials.style.display = 'none';
      } else {
        modalDocImg.style.display = 'none';
        if (modalDocInitials) {
          modalDocInitials.textContent = currentDoctor.initials;
          modalDocInitials.style.display = 'block';
        }
      }
    }
    if (modalDocName) modalDocName.textContent = currentDoctor.name;
    if (modalDocRating) modalDocRating.textContent = currentDoctor.rating;
    if (modalDocSpecialty) modalDocSpecialty.textContent = currentDoctor.specialty;
    if (modalDocSchedule) modalDocSchedule.textContent = `${selectedDayName} ${selectedDate} · ${selectedTime}`;
    if (modalDocPrice) modalDocPrice.textContent = currentDoctor.price;

    // Clear error
    if (symptomsError) symptomsError.classList.add('hidden');
    if (regSymptoms) regSymptoms.classList.remove('!border-rose-500');

    // Display modal
    if (bookingModalBackdrop) {
      bookingModalBackdrop.classList.remove('hidden');
      // slight delay for transition
      requestAnimationFrame(() => {
        bookingModalBackdrop.classList.add('open');
      });
      document.body.style.overflow = 'hidden';
    }
  }

  function closeBookingModal() {
    if (bookingModalBackdrop) {
      bookingModalBackdrop.classList.remove('open');
      setTimeout(() => {
        bookingModalBackdrop.classList.add('hidden');
        document.body.style.overflow = '';
      }, 200);
    }
  }

  // Quick symptom chips handling (toggle/append to textarea)
  quickSymptomsChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const sym = chip.dataset.symptom;
      if (!regSymptoms) return;

      let currentVal = regSymptoms.value.trim();
      if (!currentVal) {
        regSymptoms.value = sym;
        chip.classList.add('added');
      } else if (!currentVal.toLowerCase().includes(sym.toLowerCase())) {
        regSymptoms.value = `${currentVal}, ${sym}`;
        chip.classList.add('added');
      } else {
        // Remove if clicked again
        const regex = new RegExp(`(^|,\\s*)${sym}(,\\s*|$)`, 'i');
        regSymptoms.value = currentVal.replace(regex, '$1').replace(/(^,\s*|,\s*$)/, '').trim();
        chip.classList.remove('added');
      }

      // Clear error
      if (symptomsError) symptomsError.classList.add('hidden');
      if (regSymptoms) regSymptoms.classList.remove('!border-rose-500');
    });
  });

  // Duration selection pills
  durationPills.forEach(pill => {
    pill.addEventListener('click', () => {
      durationPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      if (regDuration) regDuration.value = pill.dataset.duration;
    });
  });

  // Severity selection pills
  severityPills.forEach(pill => {
    pill.addEventListener('click', () => {
      severityPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      if (regSeverity) regSeverity.value = pill.dataset.severity;
    });
  });

  // Modal Close Events
  if (btnCloseModal) btnCloseModal.addEventListener('click', closeBookingModal);
  if (btnCancelBooking) btnCancelBooking.addEventListener('click', closeBookingModal);
  if (bookingModalBackdrop) {
    bookingModalBackdrop.addEventListener('click', (e) => {
      if (e.target === bookingModalBackdrop) {
        closeBookingModal();
      }
    });
  }

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && bookingModalBackdrop && bookingModalBackdrop.classList.contains('open')) {
      closeBookingModal();
    }
  });

  // Form submission and validation
  if (btnConfirmBooking) {
    btnConfirmBooking.addEventListener('click', () => {
      const symptomsVal = regSymptoms ? regSymptoms.value.trim() : '';
      if (!symptomsVal) {
        if (symptomsError) symptomsError.classList.remove('hidden');
        if (regSymptoms) {
          regSymptoms.classList.add('!border-rose-500');
          regSymptoms.focus();
        }
        return;
      }

      const patientName = regPatientName ? regPatientName.value.trim() : 'Pasien';
      const patientPhone = regPatientPhone ? regPatientPhone.value.trim() : '0812-9988-7766';
      const durationVal = regDuration ? regDuration.value : '1 - 3 Hari';
      const severityVal = regSeverity ? regSeverity.value : 'Sedang';
      const notesVal = regNotes ? regNotes.value.trim() : '';

      // Persist booking to database via API
      fetch('/booking', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          patient_name: patientName,
          phone: patientPhone,
          symptoms: symptomsVal,
          duration: durationVal,
          severity: severityVal,
          notes: notesVal,
          doctor_id: currentDoctor?.id,
          booking_time: selectedTime,
          booking_date: `${selectedDayName} ${selectedDate}, 2026`
        })
      }).catch(() => {});

      // Visual feedback loading state
      const originalText = btnConfirmBooking.innerHTML;
      btnConfirmBooking.disabled = true;
      btnConfirmBooking.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Memproses...</span>
      `;

      setTimeout(() => {
        // Update "Next Appointment" cards in UI to reflect newly registered appointment
        const nextApptDesktop = document.querySelector('.next-appt-card');
        if (nextApptDesktop && currentDoctor) {
          const docImg = nextApptDesktop.querySelector('img');
          const docName = nextApptDesktop.querySelector('b');
          const docSpecialty = nextApptDesktop.querySelectorAll('small')[1];
          const docDateTime = nextApptDesktop.querySelectorAll('small')[2];

          if (docImg && currentDoctor.image) docImg.src = currentDoctor.image;
          if (docName) docName.textContent = currentDoctor.name;
          if (docSpecialty) docSpecialty.textContent = currentDoctor.specialty;
          if (docDateTime) docDateTime.textContent = `${selectedDayName} ${selectedDate}, 2026 · ${selectedTime}`;
        }

        const nextApptMobile = document.querySelector('.stacked-card');
        if (nextApptMobile && currentDoctor) {
          const mDocImg = nextApptMobile.querySelector('img');
          const mDocName = nextApptMobile.querySelector('b');
          const mDocSpecialty = nextApptMobile.querySelectorAll('small')[0];
          const mDocDateTime = nextApptMobile.querySelectorAll('small')[1];

          if (mDocImg && currentDoctor.image) mDocImg.src = currentDoctor.image;
          if (mDocName) mDocName.textContent = currentDoctor.name;
          if (mDocSpecialty) mDocSpecialty.textContent = currentDoctor.specialty;
          if (mDocDateTime) mDocDateTime.textContent = `${selectedDayName} ${selectedDate}, 2026 · ${selectedTime}`;
        }

        // Automatically switch card to 'before' (Gambar 3: NEXT APPOINTMENT Upcoming)
        setAppointmentMode('before', false);

        // Show Rich Success Toast
        showToast(`Pendaftaran Berhasil! Sesi dengan ${currentDoctor.name} dijadwalkan pada ${selectedDayName} ${selectedDate} · ${selectedTime} (${durationVal})`);

        // Reset button
        btnConfirmBooking.disabled = false;
        btnConfirmBooking.innerHTML = originalText;

        // Reset inputs
        if (regSymptoms) regSymptoms.value = '';
        if (regNotes) regNotes.value = '';
        quickSymptomsChips.forEach(c => c.classList.remove('added'));

        // Close modal and mobile sheet
        closeBookingModal();
        closeMobileSheet();
      }, 500);
    });
  }

  // Booking Buttons Click -> Open Registration Modal
  const bookBtns = document.querySelectorAll('.btn-book-session');
  bookBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      openBookingModal();
    });
  });

  // Reschedule & Join Call Buttons
  const rescheduleBtns = document.querySelectorAll('.btn-reschedule');
  rescheduleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      showToast('Permintaan jadwal ulang (Reschedule) telah dikirimkan ke dokter.');
    });
  });

  const joinCallBtns = document.querySelectorAll('.btn-join-call');
  joinCallBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      showToast('Menghubungkan panggilan video konsultasi...');
    });
  });

  // Theme Toggle
  const themeToggle = document.getElementById('theme-toggle');
  const updateThemeIcon = (theme) => {
    if (!themeToggle) return;
    const iconUse = themeToggle.querySelector('use');
    if (iconUse) {
      iconUse.setAttribute('href', theme === 'dark' ? '#icon-sun' : '#icon-moon');
    }
  };

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeIcon(newTheme);
      showToast(`Mode tampilan diubah ke ${newTheme === 'dark' ? 'Gelap' : 'Terang'}`);
    });
  }

  // Reset and ensure default theme is strictly LIGHT
  let currentThemeSetting = localStorage.getItem('theme');
  // If it was previously defaulted to dark, reset to light
  if (!currentThemeSetting || currentThemeSetting === 'dark') {
    currentThemeSetting = 'light';
    localStorage.setItem('theme', 'light');
  }
  document.documentElement.setAttribute('data-theme', currentThemeSetting);
  updateThemeIcon(currentThemeSetting);

  // Mobile search toggle
  const mobileSearchTrigger = document.getElementById('mobile-search-trigger');
  const mobileSearchBar = document.getElementById('mobile-search-bar');
  if (mobileSearchTrigger && mobileSearchBar) {
    mobileSearchTrigger.addEventListener('click', () => {
      mobileSearchBar.classList.toggle('hidden');
      if (!mobileSearchBar.classList.contains('hidden')) {
        mobileSearchInput?.focus();
      }
    });
  }

  // ==========================================================================
  // KLINIK MARISTELLA: AUTOMATIC APPOINTMENT STATUS & BILLING DRAWER / MODAL
  // ==========================================================================

  const desktopStateWelcome = document.getElementById('desktop-state-welcome');
  const mobileStateWelcome = document.getElementById('mobile-state-welcome');
  const desktopStateBefore = document.getElementById('desktop-state-before');
  const desktopStateAfter = document.getElementById('desktop-state-after');
  const mobileStateBefore = document.getElementById('mobile-state-before');
  const mobileStateAfter = document.getElementById('mobile-state-after');

  const initialModeAttr = document.querySelector('.next-appt-card')?.dataset?.appointmentMode;
  let currentAppointmentMode = initialModeAttr || 'welcome';
  let isBillPaid = (currentAppointmentMode === 'welcome');

  function setAppointmentMode(mode, showNotification = true) {
    currentAppointmentMode = mode;

    if (mode === 'welcome') {
      desktopStateWelcome?.classList.remove('hidden');
      desktopStateBefore?.classList.add('hidden');
      desktopStateAfter?.classList.add('hidden');

      mobileStateWelcome?.classList.remove('hidden');
      mobileStateBefore?.classList.add('hidden');
      mobileStateAfter?.classList.add('hidden');

      if (showNotification) {
        showToast('Tagihan telah lunas. Selamat datang di Klinik Maristella!');
      }
    } else if (mode === 'before') {
      desktopStateWelcome?.classList.add('hidden');
      desktopStateBefore?.classList.remove('hidden');
      desktopStateAfter?.classList.add('hidden');

      mobileStateWelcome?.classList.add('hidden');
      mobileStateBefore?.classList.remove('hidden');
      mobileStateAfter?.classList.add('hidden');

      if (showNotification) {
        showToast('Janji Temu Aktif: Menunggu jadwal konsultasi dokter.');
      }
    } else { // 'after'
      desktopStateWelcome?.classList.add('hidden');
      desktopStateBefore?.classList.add('hidden');
      desktopStateAfter?.classList.remove('hidden');

      mobileStateWelcome?.classList.add('hidden');
      mobileStateBefore?.classList.add('hidden');
      mobileStateAfter?.classList.remove('hidden');

      if (showNotification) {
        showToast('Pemeriksaan Selesai! Tagihan pemeriksaan Anda kini tersedia.');
      }
    }
  }

  // Expose for external or programmatic triggering
  window.setAppointmentMode = setAppointmentMode;
  window.openBookingModal = openBookingModal;

  // ==========================================================================
  // AUTOMATIC REAL-TIME EXAMINATION STATUS SYNC
  // ==========================================================================
  async function syncAppointmentStatus(notifyOnExamination = true) {
    try {
      const res = await fetch('/appointment-status', {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (data && data.mode) {
        if (data.mode === 'after') {
          window.currentBookingCode = data.booking_code || '#BILL00124';
          window.currentBookingId = data.booking_id;

          const desktopDoc = document.getElementById('desktop-after-doc-name') || document.querySelector('#desktop-state-after b');
          if (desktopDoc && data.doctor_name) desktopDoc.textContent = data.doctor_name;
          const mobileDoc = document.getElementById('mobile-after-doc-name') || document.querySelector('#mobile-state-after b');
          if (mobileDoc && data.doctor_name) mobileDoc.textContent = data.doctor_name;

          const desktopImg = document.getElementById('desktop-after-doc-img') || document.querySelector('#desktop-state-after img');
          if (desktopImg && data.image) desktopImg.src = data.image;
          const mobileImg = document.getElementById('mobile-after-doc-img') || document.querySelector('#mobile-state-after img');
          if (mobileImg && data.image) mobileImg.src = data.image;

          const desktopRef = document.getElementById('desktop-after-ref-code');
          if (desktopRef && data.booking_code) desktopRef.textContent = 'Ref: ' + data.booking_code;
          const mobileRef = document.getElementById('mobile-after-ref-code');
          if (mobileRef && data.booking_code) mobileRef.textContent = data.booking_code;

          if (data.total_amount) {
            const formattedTotal = 'Rp ' + Number(data.total_amount).toLocaleString('id-ID');
            const dTot = document.getElementById('desktop-total-display');
            if (dTot) dTot.textContent = formattedTotal;
            const mTot = document.getElementById('mobile-total-display');
            if (mTot) mTot.textContent = formattedTotal;
            const dPay = document.getElementById('desktop-pay-btn-label');
            if (dPay) dPay.textContent = `Bayar Sekarang (${formattedTotal})`;
            const mPay = document.getElementById('mobile-pay-btn-label');
            if (mPay) mPay.textContent = `Bayar (${formattedTotal})`;
          }
          if (data.tariff_plan) {
            applyTariffPlan(data.tariff_plan, data.total_amount);
          }
        } else if (data.mode === 'before') {
          window.currentBookingCode = data.booking_code;
          window.currentBookingId = data.booking_id;

          const desktopBeforeDoc = document.querySelector('#desktop-state-before b');
          if (desktopBeforeDoc && data.doctor_name) desktopBeforeDoc.textContent = data.doctor_name;
          const desktopBeforeImg = document.querySelector('#desktop-state-before img');
          if (desktopBeforeImg && data.image) desktopBeforeImg.src = data.image;

          const mobileBeforeDoc = document.querySelector('#mobile-state-before b');
          if (mobileBeforeDoc && data.doctor_name) mobileBeforeDoc.textContent = data.doctor_name;
          const mobileBeforeImg = document.querySelector('#mobile-state-before img');
          if (mobileBeforeImg && data.image) mobileBeforeImg.src = data.image;
        }

        if (data.mode !== currentAppointmentMode) {
          setAppointmentMode(data.mode, false);
          if (data.mode === 'after' && notifyOnExamination) {
            showToast('Pemeriksaan Selesai! Dokter telah menyelesaikan sesi konsultasi Anda. Tagihan siap dibayar.');
          }
        }
      }
    } catch (_) {
      // Ignore background sync errors
    }
  }

  // Poll periodically every 4 seconds to automatically detect when doctor completes examination
  setInterval(() => syncAppointmentStatus(true), 4000);

  // Listen for instant cross-tab notification when doctor submits SOAP
  window.addEventListener('storage', (event) => {
    if (event.key === 'klinik_doctor_examination_completed') {
      syncAppointmentStatus(true);
    }
  });

  // Also sync immediately when window regains focus
  window.addEventListener('focus', () => syncAppointmentStatus(true));

  // Drawer and Modal Elements
  const billOverlay = document.getElementById('overlay');
  const billDrawer = document.getElementById('drawer');
  const cashModal = document.getElementById('modal');
  const paymentSuccessModal = document.getElementById('payment-success-modal');
  const closeDrawerBtn = document.getElementById('closeDrawer');
  const closeModalBtn = document.getElementById('closeModal');
  const successCloseBtn = document.getElementById('successCloseBtn');
  const successBackBtn = document.getElementById('successBackBtn');
  const successPrintBtn = document.getElementById('successPrintBtn');
  const successAmount = document.getElementById('success-amount');
  const successAmountPaid = document.getElementById('success-amount-paid');
  const successItemTreatmentTitle = document.getElementById('success-item-treatment-title');
  const successItemTreatmentAmount = document.getElementById('success-item-treatment-amount');
  const successItemCompTitle = document.getElementById('success-item-component-title');
  const successItemCompAmount = document.getElementById('success-item-component-amount');
  const successItemMedTitle = document.getElementById('success-item-medicine-title');
  const successItemMedAmount = document.getElementById('success-item-medicine-amount');
  const successBodyTreatment = document.getElementById('success-body-treatment');
  const successBodyComponent = document.getElementById('success-body-component');
  const successBodyMedicine = document.getElementById('success-body-medicine');
  const openCashBtn = document.getElementById('openCash');
  const payBtn = document.getElementById('payBtn');
  const noteInput = document.getElementById('note');
  const noteCounter = document.getElementById('cnt');
  const amountInput = document.getElementById('amount');
  const chipButtons = document.querySelectorAll('.chip');
  const btnToggleBreakdown = document.getElementById('btn-toggle-item-breakdown');
  const itemsBreakdownPanel = document.getElementById('items-breakdown-panel');
  const viewDetailsText = document.getElementById('view-details-text');
  const btnShowMoreMethods = document.getElementById('btn-show-more-methods');
  const morePaymentMethods = document.getElementById('more-payment-methods');
  const showMoreText = document.getElementById('show-more-text');
  const showMoreIcon = document.getElementById('show-more-icon');

  function openBillDrawer() {
    billDrawer?.classList.add('show');
    billOverlay?.classList.add('show');
  }

  function closeBillDrawer() {
    billDrawer?.classList.remove('show');
    if (!cashModal?.classList.contains('show') && !paymentSuccessModal?.classList.contains('show')) {
      billOverlay?.classList.remove('show');
    }
  }

  function openCashModal() {
    cashModal?.classList.add('show');
    billOverlay?.classList.add('show');
  }

  function closeCashModal() {
    cashModal?.classList.remove('show');
    if (!billDrawer?.classList.contains('show') && !paymentSuccessModal?.classList.contains('show')) {
      billOverlay?.classList.remove('show');
    }
  }

  function openPaymentSuccessModal() {
    const rate = TARIFF_RATES[currentTariffPlan] || TARIFF_RATES.with_medicine;

    // Close any previous drawers/modals
    billDrawer?.classList.remove('show');
    cashModal?.classList.remove('show');

    // Update dynamic receipt figures & content according to chosen system rate
    if (successAmount) successAmount.textContent = rate.formatted;
    if (successAmountPaid) successAmountPaid.textContent = rate.formatted;

    if (currentTariffPlan === 'with_medicine') {
      if (successItemTreatmentTitle) successItemTreatmentTitle.textContent = 'Treatment (2)';
      if (successItemTreatmentAmount) successItemTreatmentAmount.textContent = 'Total: Rp 300.000';
      if (successBodyTreatment) {
        successBodyTreatment.innerHTML = `
          <div><span>Jasa Konsultasi Dr. Saran Mitchell</span><span>Rp 180.000</span></div>
          <div><span>Terapi Traksi &amp; Fisik Ortopedi</span><span>Rp 120.000</span></div>
        `;
      }

      if (successItemCompTitle) successItemCompTitle.textContent = 'Component used (1)';
      if (successItemCompAmount) successItemCompAmount.textContent = 'Total: Rp 120.000';
      if (successBodyComponent) {
        successBodyComponent.innerHTML = `
          <div><span>Elastic Knee Support Bandage</span><span>Rp 120.000</span></div>
        `;
      }

      if (successItemMedTitle) successItemMedTitle.textContent = 'Medicine (1)';
      if (successItemMedAmount) successItemMedAmount.textContent = 'Total: Rp 15.000';
      if (successBodyMedicine) {
        successBodyMedicine.innerHTML = `
          <div><span>Paket Analgesik &amp; Kalsium Kompleks (10 hari)</span><span>Rp 15.000</span></div>
        `;
      }
    } else {
      // consultation_only
      if (successItemTreatmentTitle) successItemTreatmentTitle.textContent = 'Treatment (1)';
      if (successItemTreatmentAmount) successItemTreatmentAmount.textContent = 'Total: Rp 180.000';
      if (successBodyTreatment) {
        successBodyTreatment.innerHTML = `
          <div><span>Jasa Konsultasi Dr. Saran Mitchell</span><span>Rp 180.000</span></div>
        `;
      }

      if (successItemCompTitle) successItemCompTitle.textContent = 'Component used (0)';
      if (successItemCompAmount) successItemCompAmount.textContent = 'Dilewati (Rp 0)';
      if (successBodyComponent) {
        successBodyComponent.innerHTML = `
          <div><span class="italic text-slate-400">Tidak ada komponen medis tambahan</span><span>Rp 0</span></div>
        `;
      }

      if (successItemMedTitle) successItemMedTitle.textContent = 'Medicine (0)';
      if (successItemMedAmount) successItemMedAmount.textContent = 'Tanpa Obat (Rp 0)';
      if (successBodyMedicine) {
        successBodyMedicine.innerHTML = `
          <div><span class="italic text-slate-400">Pemeriksaan konsultasi saja tanpa resep obat</span><span>Rp 0</span></div>
        `;
      }
    }

    billOverlay?.classList.add('show');
    paymentSuccessModal?.classList.add('show');
  }

  function closePaymentSuccessModal() {
    paymentSuccessModal?.classList.remove('show');
    if (!billDrawer?.classList.contains('show') && !cashModal?.classList.contains('show')) {
      billOverlay?.classList.remove('show');
    }
  }

  window.openPaymentSuccessModal = openPaymentSuccessModal;
  window.closePaymentSuccessModal = closePaymentSuccessModal;

  function closeAllBillPopups() {
    billDrawer?.classList.remove('show');
    cashModal?.classList.remove('show');
    paymentSuccessModal?.classList.remove('show');
    billOverlay?.classList.remove('show');
  }

  // Trigger buttons on Next Appointment Card
  document.querySelectorAll('.btn-open-bill-drawer').forEach(btn => {
    btn.addEventListener('click', openBillDrawer);
  });

  document.querySelectorAll('.btn-trigger-payment').forEach(btn => {
    btn.addEventListener('click', () => {
      openBillDrawer();
      setTimeout(() => {
        openCashModal();
      }, 250);
    });
  });

  document.querySelectorAll('.btn-open-success-modal').forEach(btn => {
    btn.addEventListener('click', openPaymentSuccessModal);
  });

  document.querySelectorAll('.btn-print-receipt').forEach(btn => {
    btn.addEventListener('click', () => {
      openPaymentSuccessModal();
      setTimeout(() => {
        window.print();
      }, 350);
    });
  });

  closeDrawerBtn?.addEventListener('click', closeBillDrawer);
  closeModalBtn?.addEventListener('click', closeCashModal);
  successCloseBtn?.addEventListener('click', closePaymentSuccessModal);
  successBackBtn?.addEventListener('click', closePaymentSuccessModal);
  successPrintBtn?.addEventListener('click', () => window.print());

  // Accordion inside payment success modal
  document.querySelectorAll('.success-modal .acc-head').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.acc-item');
      if (item) {
        const isOpen = item.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      }
    });
  });

  billOverlay?.addEventListener('click', () => {
    if (paymentSuccessModal?.classList.contains('show')) {
      closePaymentSuccessModal();
    } else if (cashModal?.classList.contains('show')) {
      closeCashModal();
    } else {
      closeAllBillPopups();
    }
  });

  openCashBtn?.addEventListener('click', openCashModal);

  // Keyboard accessibility
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (paymentSuccessModal?.classList.contains('show')) {
        closePaymentSuccessModal();
      } else if (cashModal?.classList.contains('show')) {
        closeCashModal();
      } else if (billDrawer?.classList.contains('show')) {
        closeBillDrawer();
      }
    }
  });

  // Note Character Counter
  if (noteInput && noteCounter) {
    noteInput.addEventListener('input', (e) => {
      noteCounter.textContent = `${e.target.value.length}/200`;
    });
  }

  // ==========================================================================
  // CLINIC SYSTEM TARIFF PLAN MANAGEMENT (Determined strictly by clinic rates)
  // ==========================================================================

  let currentTariffPlan = 'with_medicine'; // 'with_medicine' (Rp 435.000) | 'consultation_only' (Rp 180.000)
  const TARIFF_RATES = {
    with_medicine: {
      amount: 435000.00,
      formatted: 'Rp 435.000',
      serviceDesc: 'Layanan Poliklinik & Resep Obat Farmasi',
      packageName: 'Konsultasi + Resep Obat Farmasi',
      treatmentTitle: 'Treatment (2)',
      treatmentDesc: 'Konsultasi Dokter Ortopedi & Terapi Sendi',
      treatmentAmount: 'Total: Rp 300.000',
      componentAmount: 'Total: Rp 120.000',
      medicineAmount: 'Total: Rp 15.000'
    },
    consultation_only: {
      amount: 180000.00,
      formatted: 'Rp 180.000',
      serviceDesc: 'Hanya Konsultasi Dokter Spesialis (Tanpa Resep Obat)',
      packageName: 'Hanya Konsultasi Dokter Saja',
      treatmentTitle: 'Treatment (1)',
      treatmentDesc: 'Konsultasi Dokter Spesialis Ortopedi',
      treatmentAmount: 'Total: Rp 180.000',
      componentAmount: 'Dilewati (Rp 0)',
      medicineAmount: 'Tanpa Obat (Rp 0)'
    }
  };

  function applyTariffPlan(plan, customTotal = null) {
    currentTariffPlan = plan;
    const rate = TARIFF_RATES[plan] || TARIFF_RATES.with_medicine;
    const formattedAmount = customTotal ? ('Rp ' + Number(customTotal).toLocaleString('id-ID')) : rate.formatted;

    // 1. Update Card displays (Desktop & Mobile)
    const desktopTotalDisplay = document.getElementById('desktop-total-display');
    if (desktopTotalDisplay) desktopTotalDisplay.textContent = formattedAmount;

    const mobileTotalDisplay = document.getElementById('mobile-total-display');
    if (mobileTotalDisplay) mobileTotalDisplay.textContent = formattedAmount;

    const desktopServiceDesc = document.getElementById('desktop-service-desc');
    if (desktopServiceDesc) desktopServiceDesc.textContent = rate.serviceDesc;

    const desktopPayBtnLabel = document.getElementById('desktop-pay-btn-label');
    if (desktopPayBtnLabel) desktopPayBtnLabel.textContent = `Bayar Sekarang (${formattedAmount})`;

    const mobilePayBtnLabel = document.getElementById('mobile-pay-btn-label');
    if (mobilePayBtnLabel) mobilePayBtnLabel.textContent = `Bayar (${formattedAmount})`;

    const dtLabel = document.getElementById('desktop-tariff-label');
    if (dtLabel) dtLabel.textContent = plan === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter';

    const mtLabel = document.getElementById('mobile-tariff-label');
    if (mtLabel) mtLabel.textContent = plan === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter';

    // 2. Update Drawer Displays
    const drawerTariffName = document.getElementById('drawer-tariff-name');
    if (drawerTariffName) drawerTariffName.textContent = plan === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter';

    const drawerTariffBadgePrice = document.getElementById('drawer-tariff-badge-price');
    if (drawerTariffBadgePrice) drawerTariffBadgePrice.textContent = formattedAmount;

    const dItemTreatmentTitle = document.getElementById('drawer-item-treatment-title');
    if (dItemTreatmentTitle) dItemTreatmentTitle.textContent = rate.treatmentTitle;

    const dItemTreatmentDesc = document.getElementById('drawer-item-treatment-desc');
    if (dItemTreatmentDesc) dItemTreatmentDesc.textContent = rate.treatmentDesc;

    const dItemTreatmentAmt = document.getElementById('drawer-item-treatment-amount');
    if (dItemTreatmentAmt) dItemTreatmentAmt.textContent = rate.treatmentAmount;

    const dItemCompAmt = document.getElementById('drawer-item-component-amount');
    if (dItemCompAmt) dItemCompAmt.textContent = rate.componentAmount;

    const dItemMedAmt = document.getElementById('drawer-item-medicine-amount');
    if (dItemMedAmt) dItemMedAmt.textContent = rate.medicineAmount;

    const dSubtotal = document.getElementById('drawer-subtotal-display');
    if (dSubtotal) dSubtotal.textContent = formattedAmount;

    const dTotal = document.getElementById('drawer-total-display');
    if (dTotal) dTotal.textContent = formattedAmount;

    // 3. Update Modal Displays
    const modalTotalDisplay = document.getElementById('modal-total-display');
    if (modalTotalDisplay) modalTotalDisplay.textContent = formattedAmount;

    const modalPlanName = document.getElementById('modal-plan-name');
    if (modalPlanName) modalPlanName.textContent = plan === 'with_medicine' ? 'Konsultasi + Resep Obat Klinik' : 'Hanya Konsultasi Dokter';

    const modalPlanPrice = document.getElementById('modal-plan-price');
    if (modalPlanPrice) modalPlanPrice.textContent = formattedAmount;

    const modalPlanDesc = document.getElementById('modal-plan-desc');
    if (modalPlanDesc) {
      modalPlanDesc.textContent = plan === 'with_medicine'
        ? 'Dokter meresepkan tindakan medis beserta obat farmasi klinik.'
        : 'Dokter menetapkan sesi konsultasi/edukasi tanpa resep obat farmasi klinik.';
    }

    if (payBtn) {
      const spanEl = payBtn.querySelector('span');
      if (spanEl) spanEl.textContent = `Bayar ${formattedAmount}`;
    }
  }

  // Pay Confirmation Process (Saves to database and transforms card to Welcome state)
  payBtn?.addEventListener('click', () => {
    const rate = TARIFF_RATES[currentTariffPlan] || TARIFF_RATES.with_medicine;
    const originalText = payBtn.innerHTML;
    payBtn.disabled = true;
    payBtn.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span>Menyimpan Pembayaran...</span>
    `;

    fetch('/payment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        booking_id: window.currentBookingId || null,
        invoice_number: window.currentBookingCode || '#BILL00124',
        tariff_plan: currentTariffPlan,
        amount: rate.amount || 435000.00,
        payment_method: 'cash',
        notes: noteInput ? noteInput.value : 'Pembayaran tagihan via portal pasien'
      })
    })
      .then(res => res.json())
      .then(data => {
        isBillPaid = true;
        payBtn.disabled = false;
        payBtn.innerHTML = originalText;

        closeAllBillPopups();

        // Transforms card to 'welcome' (removes Tagihan card)
        setAppointmentMode('welcome', false);

        showToast(`Pembayaran ${rate.formatted} (${rate.packageName}) berhasil dicatat di database!`);

        // Automatically display the Payment Success Receipt Modal
        setTimeout(() => {
          openPaymentSuccessModal();
        }, 300);

        try {
          localStorage.setItem('klinik_payment_completed', Date.now().toString());
        } catch (e) {}
      })
      .catch(() => {
        isBillPaid = true;
        payBtn.disabled = false;
        payBtn.innerHTML = originalText;

        closeAllBillPopups();
        setAppointmentMode('welcome', false);
        showToast(`Pembayaran ${rate.formatted} (${rate.packageName}) telah lunas.`);

        setTimeout(() => {
          openPaymentSuccessModal();
        }, 300);
      });
  });

  window.printInvoiceReceipt = function() {
    openPaymentSuccessModal();
    showToast('Mencetak kuitansi resmi Klinik Maristella #BILL00124...');
    setTimeout(() => {
      window.print();
    }, 400);
  };
});

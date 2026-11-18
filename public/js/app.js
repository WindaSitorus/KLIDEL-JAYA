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
  
  const initialActiveDay = document.querySelector('.day-pill.active:not(.disabled)') || document.querySelector('.day-pill:not(.disabled)');
  let selectedDate = initialActiveDay ? initialActiveDay.dataset.date : String(new Date().getDate()).padStart(2, '0');
  let selectedDayName = initialActiveDay ? initialActiveDay.dataset.day : 'We';
  let selectedFullDate = initialActiveDay ? initialActiveDay.dataset.fullDate : (new Date().toISOString().split('T')[0]);
  let selectedIsToday = initialActiveDay ? (initialActiveDay.dataset.isToday === '1') : true;

  const initialActiveSlot = document.querySelector('.slot-pill.active:not(.disabled)') || document.querySelector('.slot-pill:not(.disabled)');
  let selectedTime = initialActiveSlot ? initialActiveSlot.dataset.time : '01 PM';
  let weekOffset = 0;
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

  // ==========================================
  // Real-Time Date & Time Picker Logic
  // ==========================================
  const dayNamesShort = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

  function updateTimeSlotsAvailability(isToday) {
    const currentHour = new Date().getHours();
    const allSlotPills = document.querySelectorAll('.slot-pill');
    let firstValidSlot = null;
    let currentSelectedValid = false;

    allSlotPills.forEach(pill => {
      const slotHour = parseInt(pill.dataset.hour, 10);
      const isPast = isToday && (slotHour <= currentHour);

      if (isPast) {
        pill.classList.add('disabled', 'opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        pill.classList.remove('active');
        pill.setAttribute('disabled', 'disabled');
        pill.setAttribute('aria-disabled', 'true');
        pill.dataset.isPast = '1';
        pill.title = 'Jam ini sudah terlewati';
      } else {
        pill.classList.remove('disabled', 'opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        pill.removeAttribute('disabled');
        pill.removeAttribute('aria-disabled');
        pill.dataset.isPast = '0';
        pill.title = pill.dataset.time;

        if (!firstValidSlot) firstValidSlot = pill.dataset.time;
        if (pill.dataset.time === selectedTime) currentSelectedValid = true;
      }
    });

    if (!currentSelectedValid && firstValidSlot) {
      selectedTime = firstValidSlot;
    }

    allSlotPills.forEach(pill => {
      if (pill.dataset.time === selectedTime && pill.dataset.isPast !== '1') {
        pill.classList.add('active');
      } else {
        pill.classList.remove('active');
      }
    });
  }

  function handleDayPillClick(btn) {
    if (btn.disabled || btn.classList.contains('disabled') || btn.dataset.isPast === '1') {
      showToast('Tanggal ini sudah terlewati dan tidak dapat dibooking.', '⚠️');
      return;
    }

    selectedDate = btn.dataset.date;
    selectedDayName = btn.dataset.day;
    selectedFullDate = btn.dataset.fullDate;
    selectedIsToday = (btn.dataset.isToday === '1');

    document.querySelectorAll('.day-pill').forEach(b => {
      if (b.dataset.fullDate === selectedFullDate && b.dataset.isPast !== '1') {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });

    updateTimeSlotsAvailability(selectedIsToday);

    if (modalDocSchedule) {
      modalDocSchedule.textContent = `${selectedDayName} ${selectedDate} · ${selectedTime}`;
    }
  }

  function handleSlotPillClick(btn) {
    if (btn.disabled || btn.classList.contains('disabled') || btn.dataset.isPast === '1') {
      showToast('Jam konsultasi ini sudah terlewati.', '⚠️');
      return;
    }

    selectedTime = btn.dataset.time;
    document.querySelectorAll('.slot-pill').forEach(b => {
      if (b.dataset.time === selectedTime && b.dataset.isPast !== '1') {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });

    if (modalDocSchedule) {
      modalDocSchedule.textContent = `${selectedDayName} ${selectedDate} · ${selectedTime}`;
    }
  }

  function renderCalendarWeek(offset) {
    weekOffset = offset;
    const now = new Date();
    const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

    // Compute Sunday of target week
    const currentDayOfWeek = now.getDay(); // 0 is Sunday
    const startOfWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() - currentDayOfWeek + (offset * 7));

    const daysData = [];
    let startMonth = startOfWeek.getMonth();
    let endMonth = startOfWeek.getMonth();
    let startYear = startOfWeek.getFullYear();

    for (let i = 0; i < 7; i++) {
      const d = new Date(startOfWeek.getFullYear(), startOfWeek.getMonth(), startOfWeek.getDate() + i);
      const iso = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
      const isPast = iso < todayStr;
      const isToday = iso === todayStr;
      endMonth = d.getMonth();

      daysData.push({
        name: dayNamesShort[d.getDay()],
        date: String(d.getDate()).padStart(2, '0'),
        fullDate: iso,
        isPast: isPast,
        isToday: isToday
      });
    }

    // Month & year label
    let monthLabel = monthNames[startMonth] + ' ' + startYear;
    if (startMonth !== endMonth) {
      monthLabel = monthNames[startMonth] + ' - ' + monthNames[endMonth] + ' ' + startYear;
    }
    document.querySelectorAll('.week-month-label').forEach(el => {
      el.textContent = monthLabel;
    });

    // Disable prev week button if weekOffset <= 0
    document.querySelectorAll('.btn-prev-week').forEach(btn => {
      btn.disabled = (weekOffset <= 0);
    });

    // Check if selectedFullDate is still in the visible week, otherwise pick first non-past
    const dateStillInView = daysData.find(d => d.fullDate === selectedFullDate && !d.isPast);
    if (!dateStillInView) {
      const firstAvailable = daysData.find(d => !d.isPast);
      if (firstAvailable) {
        selectedFullDate = firstAvailable.fullDate;
        selectedDate = firstAvailable.date;
        selectedDayName = firstAvailable.name;
        selectedIsToday = firstAvailable.isToday;
      }
    }

    // Render into containers
    const containers = [
      { id: 'desktop-days-container', isMobile: false },
      { id: 'mobile-days-container', isMobile: true }
    ];

    containers.forEach(({ id, isMobile }) => {
      const container = document.getElementById(id);
      if (!container) return;

      container.innerHTML = daysData.map(d => {
        const isActive = (d.fullDate === selectedFullDate);
        const disabledAttr = d.isPast ? 'disabled aria-disabled="true"' : '';
        const disabledClass = d.isPast ? 'disabled opacity-40 cursor-not-allowed pointer-events-none' : '';
        const activeClass = isActive && !d.isPast ? 'active' : '';
        const title = d.isPast ? 'Tanggal sudah terlewati' : (d.isToday ? 'Hari ini' : d.fullDate);
        const sizeClass = isMobile ? 'text-[10px] py-1' : '';
        const smallSize = isMobile ? 'text-[8.5px]' : '';
        const boldSize = isMobile ? 'text-[13px]' : '';

        return `
          <button type="button"
            class="day-pill ${sizeClass} ${disabledClass} ${activeClass}"
            data-day="${d.name}"
            data-date="${d.date}"
            data-full-date="${d.fullDate}"
            data-is-past="${d.isPast ? '1' : '0'}"
            data-is-today="${d.isToday ? '1' : '0'}"
            ${disabledAttr}
            title="${title}">
            <small class="${smallSize}">${d.name}</small>
            <b class="${boldSize}">${d.date}</b>
          </button>
        `;
      }).join('');
    });

    // Re-attach listeners to day-pills
    document.querySelectorAll('.days-container .day-pill').forEach(btn => {
      btn.addEventListener('click', () => handleDayPillClick(btn));
    });

    // Update time slots
    updateTimeSlotsAvailability(selectedIsToday);
  }

  // Initial event bindings for day and slot buttons
  document.querySelectorAll('.days-container .day-pill').forEach(btn => {
    btn.addEventListener('click', () => handleDayPillClick(btn));
  });

  document.querySelectorAll('.slots-container .slot-pill').forEach(btn => {
    btn.addEventListener('click', () => handleSlotPillClick(btn));
  });

  // Week navigation button listeners
  document.querySelectorAll('.btn-prev-week').forEach(btn => {
    btn.addEventListener('click', () => {
      if (weekOffset > 0) {
        renderCalendarWeek(weekOffset - 1);
      }
    });
  });

  document.querySelectorAll('.btn-next-week').forEach(btn => {
    btn.addEventListener('click', () => {
      renderCalendarWeek(weekOffset + 1);
    });
  });

  // Run initial slot availability check
  updateTimeSlotsAvailability(selectedIsToday);

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
          booking_date: selectedFullDate
        })
      })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
          const errDetail = data.errors?.booking_date?.[0]
            || data.errors?.booking_time?.[0]
            || data.errors?.jam_mulai?.[0]
            || data.message
            || 'Gagal memproses pendaftaran. Silakan periksa kembali jadwal Anda.';
          showToast(errDetail, '⚠️');
          btnConfirmBooking.disabled = false;
          btnConfirmBooking.innerHTML = originalText;
          return;
        }

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
          if (docDateTime) docDateTime.textContent = `${selectedDayName} ${selectedDate} · ${selectedTime}`;
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
          if (mDocDateTime) mDocDateTime.textContent = `${selectedDayName} ${selectedDate} · ${selectedTime}`;
        }

        // Automatically switch card to 'before' (NEXT APPOINTMENT Upcoming)
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
      })
      .catch(() => {
        showToast('Terjadi kesalahan koneksi saat memproses jadwal booking.', '⚠️');
        btnConfirmBooking.disabled = false;
        btnConfirmBooking.innerHTML = originalText;
      });
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
      document.querySelectorAll('.next-appt-card, .stacked-card, #mobile-next-appt-label').forEach(el => el.classList.add('hidden'));
      desktopStateBefore?.classList.add('hidden');
      desktopStateAfter?.classList.add('hidden');
      mobileStateBefore?.classList.add('hidden');
      mobileStateAfter?.classList.add('hidden');

      if (showNotification) {
        showToast('Tagihan telah lunas. Terima kasih atas kunjungan Anda di Klinik Maristella!');
      }
    } else if (mode === 'before') {
      document.querySelectorAll('.next-appt-card, .stacked-card, #mobile-next-appt-label').forEach(el => el.classList.remove('hidden'));
      desktopStateBefore?.classList.remove('hidden');
      desktopStateAfter?.classList.add('hidden');
      mobileStateBefore?.classList.remove('hidden');
      mobileStateAfter?.classList.add('hidden');

      if (showNotification) {
        showToast('Janji Temu Aktif: Menunggu jadwal konsultasi dokter.');
      }
    } else { // 'after'
      document.querySelectorAll('.next-appt-card, .stacked-card, #mobile-next-appt-label').forEach(el => el.classList.remove('hidden'));
      desktopStateBefore?.classList.add('hidden');
      desktopStateAfter?.classList.remove('hidden');
      mobileStateBefore?.classList.add('hidden');
      mobileStateAfter?.classList.remove('hidden');

      if (showNotification) {
        showToast('Pemeriksaan Selesai! Tagihan pemeriksaan Anda kini tersedia.');
      }
    }
  }

  // Interactive Notification Dropdown Toggle & Read Status
  const desktopNotifBtn = document.getElementById('desktop-notif-btn');
  const desktopNotifDropdown = document.getElementById('desktop-notif-dropdown');
  const mobileNotifBtn = document.getElementById('mobile-notif-btn');
  const mobileNotifDropdown = document.getElementById('mobile-notif-dropdown');

  desktopNotifBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    desktopNotifDropdown?.classList.toggle('hidden');
  });

  mobileNotifBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    mobileNotifDropdown?.classList.toggle('hidden');
  });

  document.addEventListener('click', (e) => {
    if (!desktopNotifDropdown?.contains(e.target) && !desktopNotifBtn?.contains(e.target)) {
      desktopNotifDropdown?.classList.add('hidden');
    }
    if (!mobileNotifDropdown?.contains(e.target) && !mobileNotifBtn?.contains(e.target)) {
      mobileNotifDropdown?.classList.add('hidden');
    }
  });

  window.markAllNotificationsRead = function() {
    const desktopBadge = document.getElementById('desktop-notif-badge');
    const mobileBadge = document.getElementById('mobile-notif-badge');
    const desktopCounter = document.getElementById('desktop-notif-counter');
    desktopBadge?.classList.add('hidden');
    mobileBadge?.classList.add('hidden');
    if (desktopCounter) desktopCounter.textContent = '0 Baru';
    showToast('Semua notifikasi ditandai telah dibaca.');
  };

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
          if (data.bill) {
            applyBill(data.bill);
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
  const successItemMedTitle = document.getElementById('success-item-medicine-title');
  const successItemMedAmount = document.getElementById('success-item-medicine-amount');
  const successBodyTreatment = document.getElementById('success-body-treatment');
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
    // Close any previous drawers/modals
    billDrawer?.classList.remove('show');
    cashModal?.classList.remove('show');

    // Receipt mirrors the real bill decided by the doctor (consultation + prescribed medicines)
    renderReceipt(currentBill);

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
  // PATIENT BILL (Consultation fee + medicines prescribed by the doctor)
  // Data source: `bill` payload from the server (initial render & /appointment-status)
  // ==========================================================================

  const formatRupiah = (value) => 'Rp ' + Math.round(Number(value) || 0).toLocaleString('id-ID');

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  function readInitialBill() {
    try {
      const raw = document.getElementById('bill-data')?.textContent;
      return raw ? JSON.parse(raw) : null;
    } catch (_) {
      return null;
    }
  }

  let currentBill = readInitialBill() || {
    booking_id: null,
    booking_code: '',
    doctor_name: 'Dokter Pemeriksa',
    specialty: '',
    tariff_plan: 'consultation_only',
    consultation_fee: 0,
    medicine_cost: 0,
    total_amount: 0,
    medications: []
  };

  const setText = (id, text) => {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  };

  function medicineLineHtml(med) {
    const usage = [med.dose, med.frequency].filter(Boolean).join(' · ');
    return `
      <li class="flex justify-between gap-3">
        <div class="min-w-0">
          <b class="block font-semibold text-slate-800 dark:text-slate-100">${escapeHtml(med.name)}</b>
          <small class="block text-[10.5px] text-slate-500 dark:text-slate-400">${escapeHtml(med.quantity)} ${escapeHtml(med.satuan)} × ${formatRupiah(med.harga)}</small>
          ${usage ? `<small class="block text-[10.5px] text-slate-400">Aturan pakai: ${escapeHtml(usage)}</small>` : ''}
        </div>
        <b class="font-semibold whitespace-nowrap text-slate-700 dark:text-slate-200">${formatRupiah(med.subtotal)}</b>
      </li>`;
  }

  function renderReceipt(bill) {
    const meds = Array.isArray(bill.medications) ? bill.medications : [];
    const total = formatRupiah(bill.total_amount);

    if (successAmount) successAmount.textContent = total;
    if (successAmountPaid) successAmountPaid.textContent = total;

    if (successItemTreatmentTitle) successItemTreatmentTitle.textContent = 'Jasa Konsultasi (1)';
    if (successItemTreatmentAmount) successItemTreatmentAmount.textContent = formatRupiah(bill.consultation_fee);
    if (successBodyTreatment) {
      successBodyTreatment.innerHTML = `<div><span>Jasa Konsultasi ${escapeHtml(bill.doctor_name)}</span><span>${formatRupiah(bill.consultation_fee)}</span></div>`;
    }

    if (successItemMedTitle) successItemMedTitle.textContent = `Obat (${meds.length})`;
    if (successItemMedAmount) successItemMedAmount.textContent = formatRupiah(bill.medicine_cost);
    if (successBodyMedicine) {
      successBodyMedicine.innerHTML = meds.length
        ? meds.map((med) => `<div><span>${escapeHtml(med.name)} (${escapeHtml(med.quantity)} ${escapeHtml(med.satuan)} × ${formatRupiah(med.harga)})</span><span>${formatRupiah(med.subtotal)}</span></div>`).join('')
        : '<div><span class="italic text-slate-400">Pemeriksaan konsultasi saja tanpa resep obat</span><span>Rp 0</span></div>';
    }

    document.querySelectorAll('[data-bill-code]').forEach((el) => { el.textContent = bill.booking_code || '-'; });
    document.querySelectorAll('[data-bill-specialty]').forEach((el) => { el.textContent = bill.specialty || 'Poliklinik'; });
  }

  function applyBill(bill) {
    if (!bill) return;
    currentBill = bill;

    const meds = Array.isArray(bill.medications) ? bill.medications : [];
    const hasMedicine = meds.length > 0;
    const total = formatRupiah(bill.total_amount);
    const planLabel = hasMedicine ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter';
    const medNames = meds.map((med) => med.name).join(', ');

    if (bill.booking_id) window.currentBookingId = bill.booking_id;
    if (bill.booking_code) window.currentBookingCode = bill.booking_code;

    // 1. Bill cards (desktop & mobile)
    setText('desktop-total-display', total);
    setText('mobile-total-display', total);
    setText('desktop-pay-btn-label', `Bayar Sekarang (${total})`);
    setText('mobile-pay-btn-label', `Bayar (${total})`);
    setText('desktop-tariff-label', planLabel);
    setText('mobile-tariff-label', planLabel);

    ['desktop', 'mobile'].forEach((prefix) => {
      const summary = document.getElementById(`${prefix}-medicine-summary`);
      if (!summary) return;
      summary.classList.toggle('hidden', !hasMedicine);
      summary.title = medNames;
      summary.innerHTML = `Obat: <span class="font-semibold text-slate-700 dark:text-slate-200">${escapeHtml(medNames)}</span> · ${formatRupiah(bill.medicine_cost)}`;
    });

    // 2. Bill drawer
    setText('drawer-tariff-name', planLabel);
    setText('drawer-tariff-badge-price', total);
    setText('drawer-item-treatment-title', 'Jasa Konsultasi (1)');
    setText('drawer-item-treatment-desc', bill.doctor_name + (bill.specialty ? ` · ${bill.specialty}` : ''));
    setText('drawer-item-treatment-amount', formatRupiah(bill.consultation_fee));
    setText('drawer-item-medicine-title', `Obat (${meds.length})`);
    setText('drawer-item-medicine-desc', hasMedicine ? medNames : 'Tidak ada resep obat dari dokter');
    setText('drawer-item-medicine-amount', formatRupiah(bill.medicine_cost));
    setText('drawer-subtotal-display', total);
    setText('drawer-total-display', total);

    document.querySelectorAll('[data-bill-doctor]').forEach((el) => { el.textContent = bill.doctor_name; });
    document.querySelectorAll('[data-bill-consultation]').forEach((el) => { el.textContent = formatRupiah(bill.consultation_fee); });

    const medicineList = document.getElementById('drawer-medicine-list');
    if (medicineList) {
      medicineList.innerHTML = hasMedicine
        ? meds.map(medicineLineHtml).join('')
        : '<li class="italic text-slate-400">Dokter tidak meresepkan obat (hanya konsultasi).</li>';
    }

    // 3. Cash payment modal
    setText('modal-total-display', total);
    setText('modal-plan-name', hasMedicine ? 'Konsultasi + Resep Obat Klinik' : 'Hanya Konsultasi Dokter');
    setText('modal-plan-price', total);
    setText('modal-plan-desc', hasMedicine
      ? `Dokter meresepkan ${meds.length} obat: ${medNames}.`
      : 'Dokter menetapkan sesi konsultasi/edukasi tanpa resep obat farmasi klinik.');
    setText('modal-pay-button-label', `Bayar ${total}`);

    // 4. Receipt
    renderReceipt(bill);
  }

  window.applyBill = applyBill;
  applyBill(currentBill);

  // Toggle itemized breakdown inside the bill drawer
  btnToggleBreakdown?.addEventListener('click', () => {
    const isHidden = itemsBreakdownPanel?.classList.toggle('hidden');
    btnToggleBreakdown.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
    if (viewDetailsText) viewDetailsText.textContent = isHidden ? 'Lihat Rincian' : 'Sembunyikan Rincian';
  });

  // Show more payment methods
  btnShowMoreMethods?.addEventListener('click', () => {
    const isHidden = morePaymentMethods?.classList.toggle('hidden');
    if (showMoreText) showMoreText.textContent = isHidden ? 'Show More' : 'Show Less';
    if (showMoreIcon) showMoreIcon.style.transform = isHidden ? '' : 'rotate(180deg)';
  });

  // Pay Confirmation Process (Saves to database and transforms card to Welcome state)
  payBtn?.addEventListener('click', () => {
    const bill = currentBill;
    const totalLabel = formatRupiah(bill.total_amount);
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
        booking_id: bill.booking_id || window.currentBookingId || null,
        invoice_number: bill.booking_code || window.currentBookingCode || null,
        tariff_plan: bill.tariff_plan,
        amount: Number(bill.total_amount) || null,
        payment_method: 'cash',
        notes: noteInput && noteInput.value ? noteInput.value : 'Pembayaran tagihan via portal pasien'
      })
    })
      .then(res => {
        if (!res.ok) throw new Error('Payment failed');
        return res.json();
      })
      .then(() => {
        isBillPaid = true;
        payBtn.disabled = false;
        payBtn.innerHTML = originalText;

        closeAllBillPopups();

        // Transforms card to 'welcome' (removes Tagihan card)
        setAppointmentMode('welcome', false);

        showToast(`Pembayaran ${totalLabel} berhasil dicatat di database!`);

        // Automatically display the Payment Success Receipt Modal
        setTimeout(() => {
          openPaymentSuccessModal();
        }, 300);

        try {
          localStorage.setItem('klinik_payment_completed', Date.now().toString());
        } catch (e) {}
      })
      .catch(() => {
        payBtn.disabled = false;
        payBtn.innerHTML = originalText;
        showToast('Pembayaran gagal disimpan. Silakan coba lagi.');
      });
  });

  window.printInvoiceReceipt = function() {
    openPaymentSuccessModal();
    showToast(`Mencetak kuitansi resmi Klinik Maristella ${currentBill.booking_code || ''}...`);
    setTimeout(() => {
      window.print();
    }, 400);
  };
});

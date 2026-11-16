<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Informasi Obat &amp; Stok Opname | Portal Apoteker Klinik Maristella</title>
  
  <link rel="icon" type="image/png" href="{{ asset('images/maristella-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <style>
    :root {
      /* Palette Sistem Klinik Maristella */
      --brand-primary: #012D68;
      --brand-primary-hover: #083E88;
      --brand-primary-tint: #EFF6FF;
      --brand-dark: #001F4D;
      
      --navy: #012D68;
      --navy-2: #083E88;
      --navy-ink: #001F4D;
      --blue: #8fbcf5;
      --blue-soft: #eff6ff;
      --bg: #f8fafc;
      --card: #ffffff;
      --line: #e2e8f0;
      --text: #0f172a;
      --muted: #64748b;
      --green: #12b76a;
      --green-bg: #e8f8ef;
      --green-line: #b6e8cd;
      --red: #e5484d;
      --red-bg: #fdecec;
      --amber: #d98a00;
      
      --sidebar-w: 240px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text);
      font-size: 13px;
      line-height: 20px;
      -webkit-font-smoothing: antialiased;
    }

    .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }

    /* LAYOUT CONTAINER */
    .app {
      display: flex;
      min-height: 100vh;
      max-width: 1440px;
      margin: 0 auto;
      background: var(--bg);
    }

    /* ====================================================================== */
    /* SIDEBAR SISTEM MENU KIRI (SESUAI DENGAN PORTAL KLINIK SEBELUMNYA)      */
    /* ====================================================================== */
    .sidebar {
      width: var(--sidebar-w);
      flex-shrink: 0;
      border-right: 1px solid var(--line);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      background: #ffffff;
      position: sticky;
      top: 0;
      height: 100vh;
      box-sizing: border-box;
      z-index: 20;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      padding: 0;
    }
    .brand img {
      height: 36px;
      width: auto;
      object-fit: contain;
    }
    .role-indicator-badge {
      display: inline-block;
      font-size: 10px;
      font-weight: 800;
      background: var(--brand-primary-tint);
      color: var(--brand-primary);
      border: 1px solid #BFDBFE;
      border-radius: 6px;
      padding: 2px 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-left: auto;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 4px;
      width: 100%;
    }

    .sidebar-nav .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 12px;
      color: var(--muted);
      font-size: 13.5px;
      font-weight: 600;
      text-decoration: none;
      text-align: left;
      width: 100%;
      background: transparent;
      border: none;
      cursor: pointer;
      position: relative;
      box-sizing: border-box;
      transition: background-color 0.15s ease, color 0.15s ease;
      font-family: inherit;
    }
    .sidebar-nav .nav-item svg {
      width: 19px;
      height: 19px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.85;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
      color: var(--muted);
      transition: color 0.15s ease;
    }
    .sidebar-nav .nav-item:hover {
      background-color: var(--blue-soft);
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item:hover svg {
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item.active {
      background-color: var(--brand-primary-tint);
      color: var(--brand-primary);
      font-weight: 700;
    }
    .sidebar-nav .nav-item.active svg {
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item .active-indicator {
      display: block;
      position: absolute;
      left: -20px;
      top: 9px;
      bottom: 9px;
      width: 3.5px;
      border-radius: 0 4px 4px 0;
      background-color: var(--brand-primary);
    }

    .nav-badge {
      margin-left: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 99px;
      background: var(--brand-primary);
      color: #fff;
    }

    .spacer { flex: 1; }

    .shift-box {
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 10px 12px;
      background: #fafbfd;
      margin-bottom: 10px;
    }
    .shift-box b {
      display: block;
      font-size: 15px;
      color: var(--navy);
      font-weight: 800;
    }
    .shift-box span {
      font-size: 11px;
      color: var(--muted);
    }

    .user-card {
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 10px;
      background: #fff;
      box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
      margin-bottom: 10px;
    }
    .avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: var(--brand-primary);
      color: #ffffff;
      display: grid;
      place-items: center;
      font-weight: 800;
      font-size: 12px;
      position: relative;
      flex-shrink: 0;
    }
    .avatar .status {
      position: absolute;
      right: 0;
      bottom: 0;
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: var(--green);
      border: 2px solid #ffffff;
    }
    .user-card strong {
      display: block;
      font-size: 12.5px;
      color: var(--text);
      font-weight: 700;
      line-height: 15px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .user-card small {
      display: block;
      font-size: 11px;
      color: var(--muted);
    }

    .logout-btn {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 9px 12px;
      border-radius: 10px;
      border: 1px solid #fee2e2;
      background: #fef2f2;
      color: #b91c1c;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s ease;
      font-family: inherit;
    }
    .logout-btn:hover {
      background: #fee2e2;
      color: #991b1b;
    }

    /* ====================================================================== */
    /* MAIN CONTENT AREA (SESUAI DENGAN DESAIN APOTEK STOK OPNAME)            */
    /* ====================================================================== */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 28px;
      border-bottom: 1px solid var(--line);
      background: #ffffff;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      letter-spacing: .08em;
      color: var(--muted);
    }
    .topbar .ok {
      color: var(--green);
      font-weight: 700;
    }

    .content {
      padding: 24px 28px 24px;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    .head {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 16px;
      flex-wrap: wrap;
    }
    .crumb {
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: var(--muted);
      letter-spacing: .1em;
      margin-bottom: 4px;
      font-weight: 600;
    }
    h1 {
      font-size: 28px;
      font-weight: 800;
      color: var(--navy);
      letter-spacing: -.02em;
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin: 0;
    }
    .pill {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      border: 1.5px solid var(--navy);
      color: var(--navy);
      padding: 4px 12px;
      border-radius: 99px;
      background: #ffffff;
    }
    .sub {
      color: var(--muted);
      margin-top: 4px;
      font-size: 13px;
    }
    .meta { text-align: right; }
    .meta b {
      font-size: 17px;
      color: var(--navy);
      display: block;
      font-weight: 800;
    }
    .meta small {
      display: block;
      color: var(--muted);
      font-size: 11.5px;
      margin-top: 2px;
    }

    .alert {
      display: flex;
      align-items: center;
      gap: 10px;
      background: var(--green-bg);
      border: 1px solid var(--green-line);
      border-radius: 12px;
      padding: 11px 16px;
      font-weight: 600;
      font-size: 13px;
      color: #0d4a2b;
    }
    .alert i {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--green);
      flex-shrink: 0;
    }

    /* GRID 2-KOLOM: 1fr 340px */
    .grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 18px;
      align-items: start;
    }
    .card {
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(11, 44, 107, .04);
      overflow: hidden;
    }

    /* KOLOM KIRI (TABEL STOK OPNAME) */
    .scope {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 16px 20px;
      border-bottom: 1px solid var(--line);
      flex-wrap: wrap;
    }
    .catbtn {
      all: unset;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 14px;
      border: 1.5px solid var(--navy);
      border-radius: 12px;
      background: var(--blue-soft);
      color: var(--navy);
      transition: background 0.15s;
    }
    .catbtn:hover { background: #dce9fb; }
    .catbtn .lbl {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      letter-spacing: .14em;
      color: var(--muted);
      font-weight: 700;
    }
    .catbtn b { font-size: 13px; font-weight: 800; }
    .scope .cnt {
      margin-left: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11.5px;
      color: var(--muted);
      font-weight: 600;
    }
    .addbtn {
      all: unset;
      cursor: pointer;
      background: var(--navy);
      color: #fff;
      font-weight: 700;
      font-size: 12.5px;
      padding: 8px 16px;
      border-radius: 11px;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: background 0.15s;
    }
    .addbtn:hover { background: var(--navy-2); }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    th {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10.5px;
      letter-spacing: .12em;
      color: var(--muted);
      text-align: left;
      padding: 13px 20px;
      font-weight: 700;
      background: #fafbfd;
      border-bottom: 1px solid var(--line);
    }
    th.r, td.r { text-align: right; }
    td {
      padding: 13px 20px;
      border-top: 1px solid var(--line);
      vertical-align: middle;
    }
    tbody tr {
      cursor: pointer;
      transition: background 0.15s;
    }
    tbody tr:hover { background: #f8faff; }
    tbody tr.sel {
      background: var(--blue-soft);
      box-shadow: inset 4px 0 0 var(--navy);
    }

    .sku .nm {
      font-weight: 700;
      font-size: 14px;
      color: var(--text);
      display: block;
    }
    .sku small {
      color: var(--muted);
      font-size: 11.5px;
      display: block;
      margin-top: 2px;
    }
    .rak {
      font-family: 'JetBrains Mono', monospace;
      font-weight: 700;
      font-size: 11.5px;
      background: var(--bg);
      border: 1px solid var(--line);
      padding: 3px 8px;
      border-radius: 6px;
      color: var(--text);
      display: inline-block;
    }
    .sys {
      font-weight: 800;
      font-size: 17px;
      color: var(--text);
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .act input {
      width: 76px;
      text-align: right;
      font: 800 17px 'Plus Jakarta Sans', sans-serif;
      color: var(--text);
      border: 1.5px solid transparent;
      background: transparent;
      border-radius: 8px;
      padding: 4px 6px;
      transition: all 0.15s ease;
    }
    .act input:hover { border-color: var(--line); background: #ffffff; }
    .act input:focus { outline: none; border-color: var(--navy); background: #ffffff; }
    .act.bad input { color: var(--red); }
    .diff {
      display: block;
      font-family: 'JetBrains Mono', monospace;
      font-size: 10.5px;
      color: var(--red);
      margin-top: 2px;
      font-weight: 600;
    }
    .diff.ok { color: var(--green); }
    .empty {
      padding: 40px;
      text-align: center;
      color: var(--muted);
      font-size: 13px;
    }

    /* KOLOM KANAN (PANEL RIWAYAT OBAT) */
    .panel { padding: 20px; }
    .panel h3 {
      font-size: 18px;
      font-weight: 800;
      color: var(--navy);
      margin: 0;
    }
    .ph {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 10px;
    }
    .ph .tag {
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: var(--navy-2);
      font-weight: 700;
      letter-spacing: .06em;
      margin: 4px 0 2px;
      display: block;
    }
    .ph small { color: var(--muted); font-size: 12px; }
    .ph .c {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10.5px;
      color: var(--muted);
      white-space: nowrap;
      margin-top: 4px;
      font-weight: 600;
    }
    .tabs {
      display: flex;
      gap: 6px;
      margin: 16px 0 6px;
      background: var(--bg);
      padding: 4px;
      border-radius: 12px;
      border: 1px solid var(--line);
    }
    .tabs button {
      all: unset;
      cursor: pointer;
      flex: 1;
      text-align: center;
      padding: 7px 0;
      border-radius: 8px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--muted);
      transition: all 0.15s ease;
    }
    .tabs button.on {
      background: #ffffff;
      color: var(--navy);
      box-shadow: 0 1px 3px rgba(11, 44, 107, .1);
    }

    .tl { list-style: none; margin-top: 10px; }
    .tl li {
      display: grid;
      grid-template-columns: 52px 1fr;
      gap: 12px;
      padding: 12px 0;
      border-top: 1px solid var(--line);
    }
    .tl li:first-child { border-top: none; }
    .tl time {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10.5px;
      color: var(--muted);
      line-height: 1.4;
      font-weight: 600;
    }
    .tl .t {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 800;
      font-size: 13.5px;
      color: var(--text);
    }
    .badge {
      font-size: 9.5px;
      font-weight: 800;
      padding: 2px 7px;
      border-radius: 5px;
      letter-spacing: .04em;
    }
    .badge.in { background: var(--green-bg); color: var(--green); }
    .badge.out { background: var(--blue-soft); color: var(--navy); }
    .tl p { color: var(--muted); font-size: 11.5px; margin-top: 3px; }
    .link {
      display: block;
      margin-top: 8px;
      padding-top: 14px;
      border-top: 1px solid var(--line);
      font-size: 12px;
      color: var(--navy);
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
    }
    .link:hover { text-decoration: underline; }

    /* TAB RESEP PASIEN & TAB LAPORAN (HIDDEN BY DEFAULT) */
    .tab-section { display: none; }
    .tab-section.active { display: block; }

    /* ORDER CARD UNTUK TAB RESEP */
    .order-card {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 18px;
      margin-bottom: 14px;
      transition: all 0.15s ease;
    }
    .order-card:hover {
      box-shadow: 0 4px 12px rgba(1, 45, 104, 0.06);
      border-color: #cbd5e1;
    }

    /* SUB-TABS FILTER RESEP PASIEN */
    .resep-subtabs {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 18px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--line);
      overflow-x: auto;
    }
    .resep-subtab-btn {
      all: unset;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 13px;
      color: var(--muted);
      background: var(--bg);
      border: 1px solid var(--line);
      transition: all 0.15s ease;
      white-space: nowrap;
    }
    .resep-subtab-btn:hover {
      background: #f1f5f9;
      color: var(--text);
    }
    .resep-subtab-btn.active {
      background: var(--navy);
      color: #ffffff;
      border-color: var(--navy);
      box-shadow: 0 2px 6px rgba(1, 45, 104, 0.2);
    }
    .resep-subtab-btn .st-badge {
      display: inline-block;
      font-size: 11px;
      font-weight: 800;
      padding: 1px 7px;
      border-radius: 999px;
      background: #e2e8f0;
      color: #475569;
    }
    .resep-subtab-btn.active .st-badge {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
    }

    /* MODAL POPUPS */
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(1, 31, 77, .5);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 50;
      backdrop-filter: blur(3px);
    }
    .modal.open { display: flex; }
    .dlg {
      background: #ffffff;
      border-radius: 18px;
      width: 100%;
      max-width: 440px;
      max-height: 88vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 24px 60px rgba(1, 45, 104, .3);
      overflow: hidden;
    }
    .dlg-h {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      border-bottom: 1px solid var(--line);
    }
    .dlg-h h4 {
      font-size: 17px;
      font-weight: 800;
      color: var(--navy);
      margin: 0;
    }
    .x {
      all: unset;
      cursor: pointer;
      width: 28px;
      height: 28px;
      border-radius: 8px;
      display: grid;
      place-items: center;
      color: var(--muted);
      font-size: 20px;
    }
    .x:hover { background: var(--bg); color: var(--text); }
    .dlg-b {
      padding: 18px 20px;
      overflow-y: auto;
    }
    .search {
      width: 100%;
      padding: 10px 14px;
      border: 1.5px solid var(--line);
      border-radius: 11px;
      font: 500 13.5px 'Plus Jakarta Sans', sans-serif;
      margin-bottom: 12px;
      box-sizing: border-box;
    }
    .search:focus, .fld input:focus, .fld select:focus {
      outline: none;
      border-color: var(--navy);
    }
    .catlist {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .catitem {
      all: unset;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 12px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 13px;
    }
    .catitem:hover { background: var(--bg); }
    .catitem.on { background: var(--blue-soft); color: var(--navy); font-weight: 700; }
    .catitem span {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10.5px;
      color: var(--muted);
      background: #ffffff;
      border: 1px solid var(--line);
      padding: 2px 7px;
      border-radius: 99px;
    }
    .frm {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }
    .fld {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    .fld.full { grid-column: 1 / -1; }
    .fld label {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      letter-spacing: .12em;
      color: var(--muted);
      font-weight: 700;
    }
    .fld input, .fld select {
      padding: 9px 12px;
      border: 1.5px solid var(--line);
      border-radius: 10px;
      font: 500 13.5px 'Plus Jakarta Sans', sans-serif;
      background: #ffffff;
      width: 100%;
      box-sizing: border-box;
    }
    .dlg-f {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding: 14px 20px;
      border-top: 1px solid var(--line);
      background: var(--bg);
    }
    .btn2 {
      all: unset;
      cursor: pointer;
      padding: 9px 18px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 12.5px;
      border: 1px solid var(--line);
      background: #ffffff;
      color: var(--muted);
      transition: all 0.15s ease;
    }
    .btn2:hover { background: var(--bg); color: var(--text); }
    .btn2.pri {
      background: var(--navy);
      border-color: var(--navy);
      color: #ffffff;
    }
    .btn2.pri:hover { background: var(--navy-2); }

    /* TOAST NOTIFIKASI */
    .toast {
      position: fixed;
      right: 24px;
      top: 60px;
      background: var(--navy);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 13px;
      box-shadow: 0 10px 30px rgba(1, 45, 104, .25);
      transform: translateY(-20px);
      opacity: 0;
      pointer-events: none;
      transition: all .25s ease;
      z-index: 100;
    }
    .toast.show {
      transform: none;
      opacity: 1;
    }

    /* RESPONSIVE */
    @media (max-width: 960px) {
      .sidebar { display: none; }
      .app { grid-template-columns: 1fr; }
      .grid { grid-template-columns: 1fr; }
      .content { padding: 18px 16px 80px; }
      
      .floating-bottom-nav {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 20px;
        display: flex;
        gap: 8px;
        background: #ffffff;
        padding: 6px 12px;
        border-radius: 999px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
        border: 1px solid var(--line);
        z-index: 40;
      }
      .floating-bottom-nav a {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: transparent;
        color: var(--muted);
        display: grid;
        place-items: center;
        transition: all 0.2s ease;
        text-decoration: none;
      }
      .floating-bottom-nav a.active {
        background: var(--brand-primary) !important;
        color: #ffffff !important;
      }
    }
    @media (min-width: 961px) {
      .floating-bottom-nav { display: none; }
    }
  </style>
</head>
<body>

  <!-- SVG Icons Collection (Identik dengan sistem portal Klinik Maristella) -->
  <svg style="display:none">
    <defs>
      <symbol id="i-pill" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></symbol>
      <symbol id="i-prescription" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></symbol>
      <symbol id="i-report" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></symbol>
      <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
      <symbol id="i-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></symbol>
    </defs>
  </svg>

  <div class="app">

    <!-- ====================================================================== -->
    <!-- 1. SISTEM MENU KIRI: IDENTIK DENGAN DESAIN SEBELUMNYA                   -->
    <!-- ====================================================================== -->
    <aside class="sidebar">
      
      <!-- Brand & Role Badge -->
      <div class="brand">
        <a href="{{ route('home') }}" title="Klinik Maristella">
          <img src="{{ asset('images/maristella-logo-horizontal.png') }}" alt="Klinik Maristella" />
        </a>
        <span class="role-indicator-badge">Apoteker</span>
      </div>

      <!-- Navigasi Apoteker: Menu Pertama adalah 'Informasi Obat' -->
      <nav class="sidebar-nav" aria-label="Navigasi Utama Apoteker">
        
        <!-- Menu 1: Informasi Obat (Utama / Aktif) -->
        <a href="#informasi-obat" class="nav-item active" id="navItemInformasi" onclick="switchApotekerTab('informasi', event)" title="Informasi & Stok Obat">
          <span class="active-indicator"></span>
          <svg><use href="#i-pill"/></svg>
          <span>Informasi Obat</span>
        </a>

        <!-- Menu 2: Resep Pasien -->
        <a href="#resep-pasien" class="nav-item" id="navItemResep" onclick="switchApotekerTab('resep', event)" title="Daftar Resep Pasien">
          <svg><use href="#i-prescription"/></svg>
          <span>Resep Pasien</span>
          @php $activeResepCount = ($stats['perlu_count'] ?? 0) + ($stats['racik_count'] ?? 0); @endphp
          <span class="nav-badge" id="resepCountBadge" style="{{ $activeResepCount > 0 ? '' : 'display:none;' }}">{{ $activeResepCount }}</span>
        </a>

        <!-- Menu 3: Laporan -->
        <a href="#laporan-obat" class="nav-item" id="navItemLaporan" onclick="switchApotekerTab('laporan', event)" title="Laporan & Riwayat Obat">
          <svg><use href="#i-report"/></svg>
          <span>Laporan & Riwayat</span>
        </a>

      </nav>

      <div class="spacer"></div>

      <!-- Informasi Terminal & Shift -->
      <div class="shift-box">
        <b class="mono">{{ $apoteker['terminal'] ?? 'APT-01' }}</b>
        <span>{{ $apoteker['shift'] ?? 'Shift Sore' }} · Terminal Aktif</span>
      </div>

      <!-- User Card Apoteker -->
      <div class="user-card" id="apotekerUserCard">
        <div class="avatar">
          {{ $apoteker['initials'] ?? 'AL' }}
          <i class="status"></i>
        </div>
        <div class="min-w-0 flex-1">
          <strong>{{ $apoteker['name'] ?? 'Amélie Laurent' }}</strong>
          <small>{{ $apoteker['role'] ?? 'Apoteker' }}</small>
        </div>
      </div>

      <!-- Logout Form -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn" title="Keluar dari Portal Apoteker">
          <svg style="width:14px;height:14px"><use href="#i-logout"/></svg>
          <span>Keluar Portal</span>
        </button>
      </form>

    </aside>

    <!-- ====================================================================== -->
    <!-- 2. MAIN CONTENT AREA (SESUAI DENGAN DESAIN APOTEK STOK OPNAME)         -->
    <!-- ====================================================================== -->
    <main class="main">
      
      <!-- Topbar Terminal Apotek -->
      <header class="topbar">
        <span>MARISTELLA · TERMINAL APOTEK</span>
        <span>{{ $apoteker['terminal'] ?? 'APT-01' }} / <span class="ok">ONLINE</span> · SYNC <span id="topbarSyncTime">{{ date('H:i') }}</span></span>
      </header>

      <!-- TAB 1: INFORMASI OBAT & STOK OPNAME (MENU UTAMA) -->
      <section class="content tab-section active" id="tabInformasi">
        
        <div class="head">
          <div>
            <div class="crumb">STOK / OPN-{{ date('md-H') }}</div>
            <h1>Stok Opname Obat <span class="pill">Berlangsung</span></h1>
            <div class="sub">{{ $apoteker['terminal'] ?? 'APT-01' }} · {{ date('d M Y') }} · {{ $apoteker['shift'] ?? 'Shift Sore' }}</div>
          </div>
          <div class="meta">
            <b id="hLines">0 jenis · 0 terhitung</b>
            <small id="syncMeta">Sinkron {{ date('H:i') }}</small>
            <small>Apoteker · {{ $apoteker['name'] }}</small>
          </div>
        </div>

        <div class="alert">
          <i></i>
          Masuk sebagai Apoteker ({{ $apoteker['name'] }}) — ketuk baris obat untuk melihat detail dan riwayat mutasi obat.
        </div>

        <div class="grid">
          
          <!-- KOLOM KIRI: DAFTAR OBAT & STOK OPNAME -->
          <section class="card">
            <div class="scope">
              <button type="button" class="catbtn" id="catBtn" onclick="openCat()">
                <span class="lbl" style="margin:0">KATEGORI</span>
                <b id="catLabel">Semua</b>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4l4 4 4-4"/></svg>
              </button>
              <span class="cnt" id="cnt">Memuat obat...</span>
              <button type="button" class="addbtn" onclick="openAdd()">+ Tambah Obat</button>
            </div>
            <table>
              <thead>
                <tr>
                  <th>JENIS OBAT</th>
                  <th>RAK</th>
                  <th class="r">STOK</th>
                </tr>
              </thead>
              <tbody id="rows">
                <!-- Di-render dinamis oleh JavaScript -->
              </tbody>
            </table>
          </section>

          <!-- KOLOM KANAN: PANEL RIWAYAT TRANSAKSI OBAT TERPILIH -->
          <aside class="card panel">
            <div class="ph">
              <div>
                <h3>Riwayat Obat</h3>
                <span class="tag" id="pTag">—</span>
                <small id="pSub">Pilih obat pada tabel di kiri</small>
              </div>
              <span class="c" id="pCnt">0 riwayat</span>
            </div>
            <div class="tabs" id="tabs">
              <button type="button" data-f="all" class="on">Semua</button>
              <button type="button" data-f="in">Masuk</button>
              <button type="button" data-f="out">Keluar</button>
            </div>
            <ul class="tl" id="tl">
              <!-- Riwayat di-render dinamis -->
            </ul>
            <a class="link" onclick="toast('Membuka log riwayat transaksi lengkap...')">Buka riwayat lengkap →</a>
          </aside>

        </div>

      </section>

      <!-- TAB 2: RESEP PASIEN (TERHUBUNG KE DATABASE DOKTER & PASIEN) -->
      <section class="content tab-section" id="tabResep">
        <div class="head">
          <div>
            <div class="crumb">RESEP / ANTRIAN DOKTER</div>
            <h1>Daftar Resep Pasien <span class="pill" id="resepHeaderPill">Aktif</span></h1>
            <div class="sub">Workflow peracikan obat terintegrasi rekam medis dokter dan apotek</div>
          </div>
          <div class="meta">
            <b id="resepTotalHeader">{{ count($orders) }} Antrean Resep</b>
            <small>{{ $stats['total_obat_resep'] ?? 0 }} Total Butir/Item Obat</small>
          </div>
        </div>

        @if(session('success'))
          <div class="alert" style="background:#e8f8ef; border-color:#b6e8cd; color:#0d4a2b;">
            <i></i> {{ session('success') }}
          </div>
        @endif

        <!-- SUB-TABS FILTER RESEP PASIEN -->
        <div class="resep-subtabs">
          <button type="button" class="resep-subtab-btn active" data-subtab="all" onclick="filterResepStatus('all')">
            <span>Semua</span>
            <span class="st-badge" id="badgeCountAll">{{ $stats['total_orders'] }}</span>
          </button>
          <button type="button" class="resep-subtab-btn" data-subtab="perlu_diproses" onclick="filterResepStatus('perlu_diproses')">
            <span>Perlu Diproses</span>
            <span class="st-badge" id="badgeCountPerlu" style="background:#fee2e2; color:#991b1b;">{{ $stats['perlu_count'] }}</span>
          </button>
          <button type="button" class="resep-subtab-btn" data-subtab="sedang_diracik" onclick="filterResepStatus('sedang_diracik')">
            <span>Sedang Diracik</span>
            <span class="st-badge" id="badgeCountRacik" style="background:#fef3c7; color:#92400e;">{{ $stats['racik_count'] }}</span>
          </button>
          <button type="button" class="resep-subtab-btn" data-subtab="selesai" onclick="filterResepStatus('selesai')">
            <span>Selesai &amp; Serahkan</span>
            <span class="st-badge" id="badgeCountSelesai" style="background:#dcfce7; color:#166534;">{{ $stats['selesai_count'] }}</span>
          </button>
        </div>

        <div class="card" style="padding: 20px;">
          <div id="resepOrdersContainer">
            @forelse($orders as $order)
              @php
                $patient = $order->patient;
                $doctor = $order->doctor;
                $plan = $order->medicalRecord?->plan ?? [];
                $meds = $plan['medications'] ?? [];
                $status = $plan['prescription_status'] ?? 'perlu_diproses';
              @endphp
              <div class="order-card" data-id="{{ $order->id }}" data-status="{{ $status }}">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; gap:10px;">
                  <div>
                    <span class="mono" style="font-weight:700; color:var(--navy); font-size:12px; background:var(--blue-soft); padding:3px 8px; border-radius:6px;">{{ $order->booking_code }}</span>
                    <h3 style="font-size:16px; font-weight:800; color:var(--text); margin-top:6px;">{{ $patient?->full_name ?? 'Pasien' }}</h3>
                    <p style="font-size:12px; color:var(--muted); margin-top:2px;">
                      MRN: <b class="mono">{{ $patient?->mrn ?? '-' }}</b> · Dokter: <b>{{ $doctor?->nama_dokter ?? '-' }}</b> ({{ $doctor?->poli?->nama_poli ?? 'Umum' }})
                    </p>
                  </div>
                  <div style="text-align:right;">
                    <div class="order-status-badge-container">
                      @if($status === 'perlu_diproses')
                        <span class="badge" style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; font-size:11px; padding:4px 10px;">⏳ PERLU DIPROSES</span>
                      @elseif($status === 'sedang_diracik')
                        <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:11px; padding:4px 10px;">🧪 SEDANG DIRACIK</span>
                      @else
                        <span class="badge in" style="font-size:11px; padding:4px 10px;">✓ SELESAI &amp; DISERAHKAN</span>
                      @endif
                    </div>
                    <small style="display:block; color:var(--muted); margin-top:4px;">{{ $order->booking_date }} · {{ $order->booking_time }}</small>
                  </div>
                </div>

                <!-- Daftar Obat di Resep -->
                <div style="background:#f8fafc; border:1px solid var(--line); border-radius:12px; padding:14px 16px; margin-bottom:14px;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <b style="font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted);">Daftar Obat &amp; Berapa Kali Penggunaan:</b>
                    <span class="mono" style="font-size:11px; color:var(--muted); font-weight:600;">{{ count($meds) }} Jenis Obat</span>
                  </div>
                  @if(is_array($meds) && count($meds) > 0)
                    <div style="display:flex; flex-direction:column; gap:8px;">
                      @foreach($meds as $med)
                        <div style="display:flex; justify-content:space-between; align-items:center; background:#ffffff; border:1px solid var(--line); border-radius:8px; padding:10px 12px; gap:12px; flex-wrap:wrap;">
                          <div>
                            <div style="font-weight:700; color:var(--navy); font-size:13px;">
                              {{ $med['name'] ?? 'Obat' }}
                              @if(!empty($med['kode_obat']))
                                <span class="mono" style="font-size:10.5px; background:var(--bg); border:1px solid var(--line); padding:2px 6px; border-radius:4px; margin-left:4px; font-weight:600; color:var(--muted);">{{ $med['kode_obat'] }}</span>
                              @endif
                            </div>
                            <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                              Dosis / Instruksi: <b>{{ $med['dose'] ?? '1 tablet' }}</b>
                            </div>
                          </div>
                          <div style="display:flex; align-items:center; gap:12px;">
                            <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:12px; font-weight:700; padding:4px 10px; border-radius:6px;">
                              Aturan Pakai: {{ $med['frequency'] ?? '3x1 sehari sesudah makan' }}
                            </div>
                            <div style="text-align:right; min-width:60px;">
                              <span class="mono" style="font-weight:800; font-size:13px; color:var(--text);">{{ $med['quantity'] ?? 10 }}</span>
                              <small style="color:var(--muted); display:block; font-size:10.5px;">{{ $med['satuan'] ?? 'Item' }}</small>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <p style="color:var(--muted); font-size:12px;">Tidak ada rincian obat khusus tercatat.</p>
                  @endif
                </div>

                <!-- Action Footer & Timeline Info -->
                <div class="order-action-footer" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; padding-top:4px;">
                  <div class="order-status-info" style="font-size:11.5px; color:var(--muted);">
                    @if($status === 'perlu_diproses')
                      <span>💡 Klik <b>ACC &amp; Mulai Racik</b> untuk mengonfirmasi resep dari dokter.</span>
                    @elseif($status === 'sedang_diracik')
                      <span style="color:#b45309; font-weight:600;">🧪 Sedang diracik oleh <b>{{ $plan['pharmacist_name'] ?? 'Apoteker' }}</b> sejak {{ $plan['acc_at'] ?? 'tadi' }}.</span>
                    @else
                      <span style="color:var(--green); font-weight:600;">✓ Diserahkan oleh <b>{{ $plan['pharmacist_name'] ?? 'Apoteker' }}</b> pada {{ $plan['completed_at'] ?? '-' }}. Stok telah dikurangkan.</span>
                    @endif
                  </div>

                  <div class="order-action-buttons" style="display:flex; gap:8px;">
                    @if($status === 'perlu_diproses')
                      <button type="button" class="btn2" onclick="cetakEtiket({{ $order->id }}, '{{ addslashes($patient?->full_name ?? 'Pasien') }}', '{{ $order->booking_code }}')">Cetak Resep</button>
                      <button type="button" class="btn2 pri" onclick="accResep({{ $order->id }})">✓ ACC &amp; Mulai Racik Obat</button>
                    @elseif($status === 'sedang_diracik')
                      <button type="button" class="btn2" onclick="cetakEtiket({{ $order->id }}, '{{ addslashes($patient?->full_name ?? 'Pasien') }}', '{{ $order->booking_code }}')">Cetak Etiket</button>
                      <button type="button" class="btn2 pri" style="background:#059669; border-color:#059669;" onclick="selesaiResep({{ $order->id }})">💊 Selesai &amp; Serahkan Obat</button>
                    @else
                      <button type="button" class="btn2 pri" onclick="cetakEtiket({{ $order->id }}, '{{ addslashes($patient?->full_name ?? 'Pasien') }}', '{{ $order->booking_code }}')">Cetak Salinan Etiket</button>
                    @endif
                  </div>
                </div>

              </div>
            @empty
              <div class="empty">
                Belum ada antrean resep yang perlu diproses saat ini.
              </div>
            @endforelse
          </div>
          <div id="resepEmptyState" style="display:none;" class="empty">
            Tidak ada antrean resep pada status ini.
          </div>
        </div>
      </section>

      <!-- TAB 3: LAPORAN & RIWAYAT -->
      <section class="content tab-section" id="tabLaporan">
        <div class="head">
          <div>
            <div class="crumb">LAPORAN / LOG AUDIT</div>
            <h1>Laporan &amp; Riwayat Farmasi</h1>
            <div class="sub">Rekapitulasi pergerakan inventaris dan riwayat obat Klinik Maristella</div>
          </div>
          <div class="meta">
            <b>{{ count($drugs) }} Katalog Obat</b>
            <small>Pembaruan Terakhir: {{ date('d M Y, H:i') }}</small>
          </div>
        </div>

        <div class="card" style="padding:24px;">
          <h3 style="font-size:17px; font-weight:800; color:var(--navy); margin-bottom:14px;">Ringkasan Stok Farmasi</h3>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
            <div style="background:var(--blue-soft); border:1px solid #bfdbfe; border-radius:12px; padding:16px;">
              <small style="color:var(--navy); font-weight:700; text-transform:uppercase; font-size:10px; letter-spacing:0.1em;">Total Jenis Obat</small>
              <div style="font-size:26px; font-weight:800; color:var(--navy); margin-top:4px;">{{ count($drugs) }}</div>
            </div>
            <div style="background:#e8f8ef; border:1px solid #b6e8cd; border-radius:12px; padding:16px;">
              <small style="color:var(--green); font-weight:700; text-transform:uppercase; font-size:10px; letter-spacing:0.1em;">Total Resep Selesai</small>
              <div style="font-size:26px; font-weight:800; color:#0d4a2b; margin-top:4px;">{{ count($orders) }}</div>
            </div>
            <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:16px;">
              <small style="color:#c2410c; font-weight:700; text-transform:uppercase; font-size:10px; letter-spacing:0.1em;">Status Terminal</small>
              <div style="font-size:26px; font-weight:800; color:#9a3412; margin-top:4px;">Online (APT-01)</div>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn2 pri" onclick="toast('Mengekspor laporan stok farmasi...')">Unduh Laporan Stok (CSV/Excel)</button>
          </div>
        </div>
      </section>

    </main>

  </div>

  <!-- FLOATING BOTTOM NAVIGATION (UNTUK LAYAR MOBILE) -->
  <nav class="floating-bottom-nav">
    <a href="#informasi" class="active" id="mNavInformasi" title="Informasi Obat" onclick="switchApotekerTab('informasi', event)">
      <svg width="20" height="20"><use href="#i-pill"/></svg>
    </a>
    <a href="#resep" id="mNavResep" title="Resep Pasien" onclick="switchApotekerTab('resep', event)">
      <svg width="20" height="20"><use href="#i-prescription"/></svg>
    </a>
    <a href="#laporan" id="mNavLaporan" title="Laporan" onclick="switchApotekerTab('laporan', event)">
      <svg width="20" height="20"><use href="#i-report"/></svg>
    </a>
  </nav>

  <!-- ====================================================================== -->
  <!-- MODAL: PILIH KATEGORI OBAT                                             -->
  <!-- ====================================================================== -->
  <div class="modal" id="catModal">
    <div class="dlg">
      <div class="dlg-h">
        <h4>Pilih Kategori</h4>
        <button type="button" class="x" onclick="closeModals()">×</button>
      </div>
      <div class="dlg-b">
        <input class="search" id="catSearch" placeholder="Cari kategori obat..." autocomplete="off">
        <div class="catlist" id="catList">
          <!-- Item kategori di-render dinamis -->
        </div>
      </div>
    </div>
  </div>

  <!-- ====================================================================== -->
  <!-- MODAL: TAMBAH OBAT KE INVENTARIS                                       -->
  <!-- ====================================================================== -->
  <div class="modal" id="addModal">
    <form class="dlg" id="addForm" style="max-width:520px" onsubmit="handleFormTambahObat(event)">
      @csrf
      <div class="dlg-h">
        <h4>Tambah Obat Baru</h4>
        <button type="button" class="x" onclick="closeModals()">×</button>
      </div>
      <div class="dlg-b">
        <div class="frm">
          <div class="fld full">
            <label>NAMA OBAT</label>
            <input name="name" required placeholder="mis. Ibuprofen 400 mg">
          </div>
          <div class="fld">
            <label>KODE OBAT</label>
            <input name="id" required placeholder="mis. IBU-400" style="text-transform:uppercase">
          </div>
          <div class="fld">
            <label>KEMASAN / SATUAN</label>
            <input name="desc" placeholder="mis. Tablet · strip isi 10">
          </div>
          <div class="fld">
            <label>KATEGORI</label>
            <input name="cat" list="catData" required placeholder="Pilih / ketik baru">
            <datalist id="catData"></datalist>
          </div>
          <div class="fld">
            <label>LOKASI RAK</label>
            <input name="rak" required placeholder="mis. A-05" style="text-transform:uppercase">
          </div>
          <div class="fld">
            <label>JUMLAH STOK</label>
            <input name="sys" type="number" min="0" value="0" required>
          </div>
        </div>
      </div>
      <div class="dlg-f">
        <button type="button" class="btn2" onclick="closeModals()">Batal</button>
        <button class="btn2 pri" type="submit">Simpan Obat</button>
      </div>
    </form>
  </div>

  <!-- ====================================================================== -->
  <!-- MODAL: CETAK ETIKET RESEP OBAT                                         -->
  <!-- ====================================================================== -->
  <div class="modal" id="etiketModal">
    <div class="dlg" style="max-width:480px">
      <div class="dlg-h">
        <h4>Etiket &amp; Salinan Resep</h4>
        <button type="button" class="x" onclick="closeModals()">×</button>
      </div>
      <div class="dlg-b" id="etiketPrintArea">
        <div style="text-align:center; border-bottom:2px dashed #cbd5e1; padding-bottom:12px; margin-bottom:12px;">
          <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--navy);">KLINIK MARISTELLA</h3>
          <p style="margin:2px 0 0; font-size:11px; color:var(--muted);">Instalasi Farmasi &amp; Apotek · Pelayanan Resep Pasien</p>
        </div>
        <div style="font-size:12px; line-height:1.6; margin-bottom:14px; background:#f8fafc; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0;">
          <div style="display:flex; justify-content:space-between;"><span>No. Booking:</span> <b id="etiketBookingCode" class="mono">-</b></div>
          <div style="display:flex; justify-content:space-between;"><span>Nama Pasien:</span> <b id="etiketPatientName">-</b></div>
          <div style="display:flex; justify-content:space-between;"><span>Waktu Cetak:</span> <span id="etiketDate">{{ date('d M Y, H:i') }}</span></div>
        </div>
        <div style="margin-bottom:14px;">
          <b style="font-size:11px; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:8px;">Rincian Obat &amp; Aturan Pakai:</b>
          <div id="etiketMedsList" style="display:flex; flex-direction:column; gap:8px;">
            <!-- Rendered by JS -->
          </div>
        </div>
        <div style="text-align:center; font-size:11px; color:var(--muted); border-top:1px dashed #cbd5e1; padding-top:10px;">
          Semoga Lekas Sembuh · Minum obat secara teratur sesuai aturan pakai
        </div>
      </div>
      <div class="dlg-f">
        <button type="button" class="btn2" onclick="closeModals()">Tutup</button>
        <button type="button" class="btn2 pri" onclick="window.print()">Cetak (Print)</button>
      </div>
    </div>
  </div>

  <!-- TOAST FEEDBACK NOTIFICATION -->
  <div class="toast" id="toast"></div>

  <!-- ====================================================================== -->
  <!-- JAVASCRIPT LOGIC INTERAKTIF & DATABASE INTEGRASI                      -->
  <!-- ====================================================================== -->
  <script>
    // Ambil data obat dari backend controller
    const DRUGS = @json($drugs ?? []);

    const cats = () => [...new Set(DRUGS.map(d => d.cat))].sort((a, b) => a.localeCompare(b, 'id'));
    let cat = 'Semua';
    let sel = DRUGS.length ? DRUGS[0].id : null;
    let filt = 'all';
    let counted = new Set(DRUGS.filter(d => d.act !== null && d.act !== undefined).map(d => d.id));

    const $ = s => document.querySelector(s);

    // SWITCH TAB NAVIGASI
    function switchApotekerTab(tabName, event) {
      if (event) event.preventDefault();

      // Sidebar links
      $('#navItemInformasi')?.classList.toggle('active', tabName === 'informasi');
      $('#navItemResep')?.classList.toggle('active', tabName === 'resep');
      $('#navItemLaporan')?.classList.toggle('active', tabName === 'laporan');

      // Mobile links
      $('#mNavInformasi')?.classList.toggle('active', tabName === 'informasi');
      $('#mNavResep')?.classList.toggle('active', tabName === 'resep');
      $('#mNavLaporan')?.classList.toggle('active', tabName === 'laporan');

      // Sections
      $('#tabInformasi')?.classList.toggle('active', tabName === 'informasi');
      $('#tabResep')?.classList.toggle('active', tabName === 'resep');
      $('#tabLaporan')?.classList.toggle('active', tabName === 'laporan');

      // Scroll top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function renderChips() {
      const lbl = $('#catLabel');
      if (lbl) lbl.textContent = cat;
    }

    function openCat() {
      const search = $('#catSearch');
      if (search) search.value = '';
      renderCatList();
      $('#catModal')?.classList.add('open');
      setTimeout(() => $('#catSearch')?.focus(), 60);
    }

    function renderCatList() {
      const q = ($('#catSearch')?.value || '').trim().toLowerCase();
      const items = [['Semua', DRUGS.length], ...cats().map(c => [c, DRUGS.filter(d => d.cat === c).length])]
        .filter(([c]) => c.toLowerCase().includes(q));

      const listEl = $('#catList');
      if (!listEl) return;

      listEl.innerHTML = items.length
        ? items.map(([c, n]) => `
            <button type="button" class="catitem ${c === cat ? 'on' : ''}" data-c="${c}">
              ${c}<span>${n}</span>
            </button>
          `).join('')
        : '<div class="empty" style="padding:20px">Kategori tidak ditemukan.</div>';

      listEl.querySelectorAll('.catitem').forEach(b => {
        b.onclick = () => {
          cat = b.dataset.c;
          const l = list();
          if (l.length && !l.find(d => d.id === sel)) sel = l[0].id;
          closeModals();
          render();
        };
      });
    }

    function openAdd() {
      $('#addForm')?.reset();
      const catData = $('#catData');
      if (catData) {
        catData.innerHTML = cats().map(c => `<option value="${c}">`).join('');
      }
      $('#addModal')?.classList.add('open');
      setTimeout(() => $('#addForm [name=name]')?.focus(), 60);
    }

    function closeModals() {
      document.querySelectorAll('.modal').forEach(m => m.classList.remove('open'));
    }

    const list = () => DRUGS.filter(d => cat === 'Semua' || d.cat === cat);

    function renderRows() {
      const l = list();
      const cntEl = $('#cnt');
      if (cntEl) {
        cntEl.textContent = `${l.length} jenis obat`;
      }

      const rowsEl = $('#rows');
      if (!rowsEl) return;

      if (!l.length) {
        rowsEl.innerHTML = '<tr><td colspan="3" class="empty">Tidak ada obat pada kategori ini.</td></tr>';
        return;
      }

      rowsEl.innerHTML = l.map(d => {
        return `
          <tr data-id="${d.id}" class="${d.id === sel ? 'sel' : ''}">
            <td class="sku">
              <span class="nm">${d.name}</span>
              <small><span class="mono">${d.id}</span> · ${d.desc}</small>
            </td>
            <td><span class="rak">${d.rak}</span></td>
            <td class="r sys">${d.sys}</td>
          </tr>
        `;
      }).join('');

      // Click row to select
      rowsEl.querySelectorAll('tr[data-id]').forEach(tr => {
        tr.onclick = () => {
          sel = tr.dataset.id;
          render();
        };
      });
    }

    function renderPanel() {
      const d = DRUGS.find(x => x.id === sel);
      if (!d) {
        if ($('#pTag')) $('#pTag').textContent = '—';
        if ($('#pSub')) $('#pSub').textContent = 'Pilih obat pada tabel di kiri';
        if ($('#pCnt')) $('#pCnt').textContent = '0 riwayat';
        if ($('#tl')) $('#tl').innerHTML = '<li><p>Belum ada riwayat obat yang dipilih.</p></li>';
        return;
      }

      if ($('#pTag')) $('#pTag').textContent = d.id;
      if ($('#pSub')) $('#pSub').textContent = `${d.name} · Rak ${d.rak}`;

      const h = (d.h || []).filter(x => filt === 'all' || x.t === filt);
      if ($('#pCnt')) $('#pCnt').textContent = `${h.length} riwayat`;

      const tl = $('#tl');
      if (tl) {
        tl.innerHTML = h.length
          ? h.map(x => `
              <li>
                <time>${x.time}<br>${x.d}</time>
                <div>
                  <div class="t">
                    <span class="badge ${x.t}">${x.t === 'in' ? 'MASUK' : 'KELUAR'}</span>
                    ${x.q > 0 ? '+' : '−'}${Math.abs(x.q)}
                  </div>
                  <p>${x.t === 'in' ? 'Dari distributor · ' : ''}${x.p} · <span class="mono">${x.r}</span></p>
                </div>
              </li>
            `).join('')
          : '<li><p style="color:var(--muted); padding:10px 0;">Belum ada riwayat pada filter ini.</p></li>';
      }
    }

    function renderCounts() {
      const hLines = $('#hLines');
      if (hLines) {
        hLines.textContent = `${DRUGS.length} jenis obat`;
      }
    }

    function render() {
      renderChips();
      renderRows();
      renderPanel();
      renderCounts();
    }

    // Filter tabs di riwayat
    document.querySelectorAll('#tabs button').forEach(b => {
      b.onclick = () => {
        filt = b.dataset.f;
        document.querySelectorAll('#tabs button').forEach(x => x.classList.toggle('on', x === b));
        renderPanel();
      };
    });

    function toast(m) {
      const t = $('#toast');
      if (!t) return;
      t.textContent = m;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2400);
    }

    // Modal listeners
    $('#catSearch')?.addEventListener('input', renderCatList);
    document.querySelectorAll('.modal').forEach(m => {
      m.addEventListener('mousedown', e => {
        if (e.target === m) closeModals();
      });
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModals();
    });

    // Tambah Obat Baru Form Handler
    function handleFormTambahObat(e) {
      e.preventDefault();
      const f = new FormData(e.target);
      const g = k => (f.get(k) || '').toString().trim();
      const id = g('id').toUpperCase();

      if (DRUGS.some(d => d.id === id)) {
        toast('Kode obat sudah terdaftar!');
        return;
      }

      const sys = parseInt(g('sys')) || 0;
      const act = sys;
      const name = g('name');
      const catVal = g('cat');
      const rak = g('rak').toUpperCase();
      const desc = g('desc') || 'Kemasan standar';

      const newDrug = {
        id,
        name,
        desc,
        cat: catVal,
        rak,
        sys,
        act,
        h: sys > 0 ? [{
          t: 'in',
          time: new Date().toTimeString().slice(0, 5),
          d: 'Hari ini',
          q: sys,
          p: 'Stok awal (input manual)',
          r: 'OPN-BARU'
        }] : []
      };

      // Simpan juga ke backend database via AJAX
      fetch("{{ route('apoteker.tambahObat') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
          kode_obat: id,
          nama_obat: name,
          kategori: catVal,
          satuan: desc,
          rak: rak,
          stok: sys
        })
      }).then(r => r.json()).then(res => {
        toast(`${name} berhasil disimpan ke database!`);
      }).catch(() => {
        toast(`${name} ditambahkan ke sesi!`);
      });

      DRUGS.push(newDrug);
      counted.add(id);
      sel = id;

      if (cat !== 'Semua' && cat !== catVal) cat = 'Semua';

      closeModals();
      render();
    }

    // Selesaikan Stok Opname
    function selesaikanOpname() {
      const diffs = DRUGS.filter(d => d.act !== d.sys);
      
      const payload = DRUGS.map(d => ({
        kode_obat: d.id,
        stok_aktual: d.act
      }));

      fetch("{{ route('apoteker.selesaikanOpname') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ items: payload })
      }).then(r => r.json()).then(res => {
        // Sinkronisasi sistem dengan aktual
        DRUGS.forEach(d => { d.sys = d.act; });
        render();
        toast('Stok Opname Selesai! Stok sistem disinkronkan.');
      }).catch(() => {
        DRUGS.forEach(d => { d.sys = d.act; });
        render();
        toast('Stok Opname diselesaikan.');
      });
    }

    // ==========================================
    // WORKFLOW RESEP PASIEN APOTEK
    // ==========================================
    let currentResepSubtab = 'all';

    function filterResepStatus(status) {
      currentResepSubtab = status;

      // Update active style pada sub-tab tombol
      document.querySelectorAll('.resep-subtab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.subtab === status);
      });

      applyResepFilter();
    }

    function applyResepFilter() {
      const cards = document.querySelectorAll('#resepOrdersContainer .order-card');
      let visibleCount = 0;

      cards.forEach(card => {
        const cardStatus = card.dataset.status;
        const isVisible = (currentResepSubtab === 'all') || (cardStatus === currentResepSubtab);
        card.style.display = isVisible ? 'block' : 'none';
        if (isVisible) visibleCount++;
      });

      const emptyState = document.getElementById('resepEmptyState');
      if (emptyState) {
        emptyState.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
      }
    }

    function updateResepBadges() {
      const cards = document.querySelectorAll('#resepOrdersContainer .order-card');
      let allCount = cards.length;
      let perluCount = 0;
      let racikCount = 0;
      let selesaiCount = 0;

      cards.forEach(card => {
        const st = card.dataset.status;
        if (st === 'perlu_diproses') perluCount++;
        else if (st === 'sedang_diracik') racikCount++;
        else if (st === 'selesai') selesaiCount++;
      });

      if ($('#badgeCountAll')) $('#badgeCountAll').textContent = allCount;
      if ($('#badgeCountPerlu')) $('#badgeCountPerlu').textContent = perluCount;
      if ($('#badgeCountRacik')) $('#badgeCountRacik').textContent = racikCount;
      if ($('#badgeCountSelesai')) $('#badgeCountSelesai').textContent = selesaiCount;
      if ($('#resepTotalHeader')) $('#resepTotalHeader').textContent = `${allCount} Antrean Resep`;

      const activeCount = perluCount + racikCount;
      const navBadge = $('#resepCountBadge');
      if (navBadge) {
        navBadge.textContent = activeCount;
        navBadge.style.display = activeCount > 0 ? '' : 'none';
      }
    }

    // ACC RESEP & MULAI RACIK
    async function accResep(bookingId) {
      const card = document.querySelector(`.order-card[data-id="${bookingId}"]`);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      toast('Mengonfirmasi & memulai peracikan...');

      try {
        const response = await fetch(`/apoteker/resep/${bookingId}/acc`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Gagal meng-ACC resep.');
        }

        if (card) {
          card.dataset.status = 'sedang_diracik';

          const badgeContainer = card.querySelector('.order-status-badge-container');
          if (badgeContainer) {
            badgeContainer.innerHTML = '<span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:11px; padding:4px 10px;">🧪 SEDANG DIRACIK</span>';
          }

          const infoEl = card.querySelector('.order-status-info');
          if (infoEl) {
            infoEl.innerHTML = '<span style="color:#b45309; font-weight:600;">🧪 Sedang diracik oleh <b>Apoteker</b> sejak baru saja.</span>';
          }

          const actionsEl = card.querySelector('.order-action-buttons');
          if (actionsEl) {
            actionsEl.innerHTML = `
              <button type="button" class="btn2" onclick="cetakEtiket(${bookingId})">Cetak Etiket</button>
              <button type="button" class="btn2 pri" style="background:#059669; border-color:#059669;" onclick="selesaiResep(${bookingId})">💊 Selesai &amp; Serahkan Obat</button>
            `;
          }
        }

        updateResepBadges();
        applyResepFilter();
        toast(data.message || 'Resep berhasil di-ACC! Obat sedang diracik.');

      } catch (err) {
        toast('Error: ' + err.message);
        console.error(err);
      }
    }

    // SELESAI & SERAHKAN OBAT
    async function selesaiResep(bookingId) {
      const card = document.querySelector(`.order-card[data-id="${bookingId}"]`);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      toast('Menyelesaikan & menyerahkan obat...');

      try {
        const response = await fetch(`/apoteker/resep/${bookingId}/selesai`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Gagal menyelesaikan resep.');
        }

        if (card) {
          card.dataset.status = 'selesai';

          const badgeContainer = card.querySelector('.order-status-badge-container');
          if (badgeContainer) {
            badgeContainer.innerHTML = '<span class="badge in" style="font-size:11px; padding:4px 10px;">✓ SELESAI &amp; DISERAHKAN</span>';
          }

          const infoEl = card.querySelector('.order-status-info');
          if (infoEl) {
            infoEl.innerHTML = '<span style="color:var(--green); font-weight:600;">✓ Diserahkan oleh <b>Apoteker</b>. Stok obat telah dikurangkan.</span>';
          }

          const actionsEl = card.querySelector('.order-action-buttons');
          if (actionsEl) {
            actionsEl.innerHTML = `
              <button type="button" class="btn2 pri" onclick="cetakEtiket(${bookingId})">Cetak Salinan Etiket</button>
            `;
          }
        }

        updateResepBadges();
        applyResepFilter();
        toast(data.message || 'Obat telah diserahkan dan stok diperbarui!');

      } catch (err) {
        toast('Error: ' + err.message);
        console.error(err);
      }
    }

    // CETAK ETIKET RESEP OBAT
    function cetakEtiket(bookingId, patientName, bookingCode) {
      const card = document.querySelector(`.order-card[data-id="${bookingId}"]`);
      if (!card) return;

      const pName = patientName || card.querySelector('h3')?.textContent || 'Pasien';
      const bCode = bookingCode || card.querySelector('.mono')?.textContent || 'RSP-001';

      if ($('#etiketBookingCode')) $('#etiketBookingCode').textContent = bCode;
      if ($('#etiketPatientName')) $('#etiketPatientName').textContent = pName;

      // Ambil daftar obat dari card
      const medsItems = card.querySelectorAll('[style*="background:#ffffff"]');
      const etiketMedsList = $('#etiketMedsList');
      if (etiketMedsList) {
        if (medsItems.length > 0) {
          etiketMedsList.innerHTML = Array.from(medsItems).map(el => {
            const name = el.querySelector('[style*="font-weight:700"]')?.textContent.trim() || 'Obat';
            const rule = el.querySelector('[style*="background:#eff6ff"]')?.textContent.trim() || '3x1';
            const qty = el.querySelector('.mono')?.textContent.trim() || '10';
            return `
              <div style="padding:6px 0; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <b style="color:var(--navy); font-size:12.5px;">${name}</b>
                  <div style="font-size:11.5px; color:#1e40af; font-weight:600; margin-top:2px;">${rule}</div>
                </div>
                <div style="font-weight:700; font-size:12px; color:var(--text);">${qty} unit</div>
              </div>
            `;
          }).join('');
        } else {
          etiketMedsList.innerHTML = '<p style="color:var(--muted); font-size:12px;">Rincian obat terapi standar.</p>';
        }
      }

      $('#etiketModal')?.classList.add('open');
    }

    // Inisialisasi awal
    document.addEventListener('DOMContentLoaded', () => {
      render();
      applyResepFilter();
    });
  </script>

</body>
</html>

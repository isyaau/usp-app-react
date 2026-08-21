 <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
 <meta name="color-scheme" content="light dark" />
 <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
 <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

 <meta name="title" content="KOPINKA KSP" />
 <meta name="author" content="ColorlibHQ" />
 <link rel="icon" type="image/png" href="{{asset('img/logo.png')}}" />
 <meta
     name="description"
     content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
 <meta
     name="keywords"
     content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />

 <meta name="supported-color-schemes" content="light dark" />
 <link rel="preload"
     href="{{ asset('AdminLTE/dist/css/adminlte.css') }}"
     as="style"
     onload="this.onload=null;this.rel='stylesheet'">
 <noscript>
     <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.css') }}">
 </noscript>


 <link
     rel="stylesheet"
     href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
     integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
     crossorigin="anonymous"
     media="print"
     onload="this.media='all'" />

 <link
     rel="stylesheet"
     href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
     crossorigin="anonymous" />

 <link
     rel="stylesheet"
     href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
     crossorigin="anonymous" />

 <link rel="stylesheet" href="{{asset('AdminLTE/dist/css/adminlte.css') }}" />

 <script src="https://kit.fontawesome.com/79facca96d.js" crossorigin="anonymous"></script>
 <link rel="stylesheet" href="{{asset('sweetalert2/dist/sweetalert2.min.css') }}" />
 <style>
     .date-wrapper {
         position: relative;
         width: 100%;
     }

     .custom-date {
         width: 100%;
         padding-right: 40px;
         /* ruang untuk icon */
     }

     /* Hilangkan icon default */
     .custom-date::-webkit-calendar-picker-indicator {
         opacity: 0;
         z-index: 10;
     }

     /* Tambahkan icon custom */
     .date-wrapper::after {
         content: "";
         position: absolute;
         right: 10px;
         top: 50%;
         width: 20px;
         height: 20px;
         transform: translateY(-50%);
         background-image: url('{{ asset("img/logo.png") }}');
         /* ganti path icon-mu */
         background-size: contain;
         background-repeat: no-repeat;
         pointer-events: none;
     }
 </style>

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
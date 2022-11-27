<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard</title>
        <link href="{{ asset('redis-manager/css/style.css') }}" rel="stylesheet" /> 
        <link href="{{ asset('redis-manager/css/styles.css') }}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="/redis-manager/dashboard">{{ config('app.name') }} - Redis Manager</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Folders</div>
                            <div class="folder-container"></div>
                        </div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">

                    </div>
                </main>
            </div>
        </div>
        <script src="{{ asset('redis-manager/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('redis-manager/js/script.js') }}"></script>
        <!-- <script src="{{ public_path('redis-manager/js/scripts.js') }}"></script> -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script> -->
        <script>
            const folderContainer = document.querySelector(".folder-container");
            const folderItemContainer = document.querySelectorAll("#folder-item");

            let isPingQueryOngoing = false;
            const queryPing = async() => await fetch('/api/redis-manager/ping');

            const pingTimenIterval = setInterval(async () => {
                if(isPingQueryOngoing) return;
                isPingQueryOngoing = true;
                const result = await queryPing();
                if (!result.ok) {
                    alert('No redis connection.');
                    clearInterval(pingTimenIterval);
                }
                isPingQueryOngoing = false;
            }, 5000);

            const queryAllFolder = async () => {
                const result = await fetch('/api/redis-manager/all-folder');
                const { data = [] } = await result.json();
                
                const folderData = data.map((data, index) => (
                    `<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" id="folder-item" data-bs-target="#${data}" aria-expanded="false" aria-controls="${data}">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                ${data}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="${data}" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">folder:1</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">folder:2</a>
                                </nav>
                            </div>`
                ));

                folderContainer.innerHTML = folderData.join('');
            };

            queryAllFolder();

        </script>
     
    </body>
</html>

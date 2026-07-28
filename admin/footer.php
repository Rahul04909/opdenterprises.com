                    <!-- Container-fluid closed -->
                    </div>
                <!-- Content section closed -->
            </section>
            <!-- Content-wrapper closed -->
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Admin Panel Designed By <strong><a target="_blank" href="https://www.prayagcomputer.in">Rahul Dhiman / +91-8059982049</a></strong>
            </div>
            <strong> &copy;<?php echo date('Y'); ?></strong> All rights reserved.
        </footer>

        <script>
            const logoutLink = document.querySelector('a.nav-link[href="logout.php"]');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You will be logged out!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, log me out!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'logout.php';
                        }
                    });
                });
            }
        </script>

        <!-- Wrapper closed -->
    </div>
    <!-- Body closed -->
</body>

</html>
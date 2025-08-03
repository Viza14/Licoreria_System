<style>
    /* ==================== FOOTER ==================== */
    .site-footer {
        background: #1d2a58;
        color: white;
        padding: 15px 0;
        border-top: 3px solid #f27c1f;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    .go-top {
        display: inline-block;
        width: 30px;
        height: 30px;
        background: #f27c1f;
        color: white;
        border-radius: 50%;
        margin-left: 10px;
        transition: all 0.3s ease;
    }

    .go-top .fa {
        line-height: 30px;
    }

    .go-top:hover {
        background: #d86600;
        transform: translateY(-3px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Efecto de hover para todos los enlaces del footer */
    .site-footer a {
        color: #e0e0e0;
        transition: color 0.3s ease;
    }

    .site-footer a:hover {
        color: #f27c1f;
        text-decoration: none;
    }
</style>    
    
    <!--footer start-->
    <footer >

    </footer>
    <!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery.scrollTo.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery.sparkline.js"></script>

<!--common script for all pages-->
<script src="<?php echo BASE_URL; ?>assets/js/common-scripts.js"></script>

<!--script for this page-->
<script src="<?php echo BASE_URL; ?>assets/js/sparkline-chart.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/zabuto_calendar.js"></script>


<script>
    // Logout confirmation with SweetAlert
    $(document).ready(function() {
        $('a.logout').on('click', function(e) {
            e.preventDefault();
            const logoutUrl = $(this).attr('href');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de cerrar tu sesión",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f27c1f',
                cancelButtonColor: '#1d2a58',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = logoutUrl;
                }
            });
        });
    });
</script>

<!-- Agrega este script al final de tu vista, antes de cerrar el body: -->
<script>
$(document).ready(function() {
    // Animación de las barras
    $('.custom-bar-chart .value').each(function() {
        var finalHeight = $(this).data('height');
        $(this).animate({
            'height': finalHeight
        }, 1000);
    });
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

</body>
</html>
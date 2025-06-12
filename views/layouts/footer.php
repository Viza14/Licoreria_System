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
<script src="<?php echo BASE_URL; ?>assets/js/jquery.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery-1.8.3.min.js"></script>
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

<!-- Contadores animados -->
<script type="text/javascript">
    // Contador para Productos
    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 2000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Contador para Clientes
    $('.count2').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 1500,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Contador para Ventas
    $('.count3').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 1200,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Contador para Ingresos
    $('.count4').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 1000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Gráfico de ventas
    var salesChartData = {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul"],
        datasets: [
            {
                fillColor: "rgba(104, 223, 240, 0.5)",
                strokeColor: "#68dff0",
                pointColor: "#fff",
                pointStrokeColor: "#68dff0",
                data: [65, 59, 90, 81, 56, 55, 40]
            }
        ]
    };
    
    var ctx = document.getElementById("sales-chart").getContext("2d");
    new Chart(ctx).Line(salesChartData, {
        responsive: true,
        maintainAspectRatio: false
    });
</script>
<script>
    // SweetAlert para el botón de cerrar sesión
    $(document).ready(function() {
        $('a.logout').on('click', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto
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

</body>
</html>
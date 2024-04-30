( function( $ ) {

    // ready event
    $( function() {
        var charts = cnDashboardArgs.charts;

        if ( Object.entries(charts).length > 0 ) {
            for ( const [key, config] of Object.entries( charts ) ) {
				// create canvas
                var canvas = document.getElementById( 'cn-' + key + '-chart' );

                if ( canvas ) {
					// options per chart type
					var options = {
						doughnut: {
							responsive: true,
							plugins: {
								legend: {
									position: 'top',
								}
							},
							hover: {
								mode: 'label'
							},
							layout: {
								padding: 0
							}
						},
						line: {
							scales: {
								x: {
									display: true,
									title: {
										display: false
									}
								},
								y: {
									display: true,
									grace: 0,
									beginAtZero: true,
									title: {
										display: false
									},
									ticks: {
										precision: 0,
										maxTicksLimit: 12
									}
								}
							}
						}
					}

                    config.options = options.hasOwnProperty( config.type ) ? options[config.type] : {};

                    var chart = new Chart( canvas, config );

                    chart.update();
                }
            }
        }
    } );

} )( jQuery );
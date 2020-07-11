<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
?>
</body>
</html>

<script>

$(document).ready(function() {
        $('#docTable').DataTable({
            "order": [[ 1, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [4,5,6] }
            ],
            "bPaginate":false,

            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\£,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                totalDay = api
                    .column( 1 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalWeek = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalMonth = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalYear = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( 4 ).footer() ).html(
                    '£'+ totalYear.toFixed(2)
                );
                $( api.column( 3 ).footer() ).html(
                    '£'+ totalMonth.toFixed(2)
                );
                $( api.column( 2 ).footer() ).html(
                    '£'+ totalWeek.toFixed(2)
                );
                $( api.column( 1 ).footer() ).html(
                    '£'+ totalDay.toFixed(2)
                );
            }


        });
    } );

</script>
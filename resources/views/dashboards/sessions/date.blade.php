
<head>

    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <script>
         $(document).ready(function ()
{
    $('#nepaliDateF').nepaliDatePicker({
        ndpEnglishInput: 'englishDateF'
    });

    $('#nepaliDateFa').nepaliDatePicker({
        ndpEnglishInput: 'englishDateFa'
    });

});
    </script>
</head>


     <script>

var date = new Date();
var cYear = date.getFullYear();
var cMonth = date.getMonth();
var cDate = date.getDate();

$(document).ready(function () {
    $("#from").datepicker({
        dateFormat: 'yy-mm-dd',
        maxDate: new Date(cYear, cMonth, cDate)
    });
});

$(document).ready(function () {
    $("#to").datepicker({
        dateFormat: 'yy-mm-dd',
        maxDate: new Date(cYear, cMonth, cDate)
    });
});
</script>

